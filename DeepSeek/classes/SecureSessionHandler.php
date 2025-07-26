<?php
class SecureSessionHandler {
    private $db;
    
    public function __construct() {
        $this->db = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        
        // Set custom session save handlers
        session_set_save_handler(
            [$this, 'open'],
            [$this, 'close'],
            [$this, 'read'],
            [$this, 'write'],
            [$this, 'destroy'],
            [$this, 'gc']
        );
    }
    
    public function open($savePath, $sessionName) {
        return true;
    }
    
    public function close() {
        return true;
    }
    
    public function read($id) {
        $stmt = $this->db->prepare('SELECT data FROM sessions WHERE session_id = ? AND expires_at > NOW()');
        $stmt->execute([$id]);
        
        if ($row = $stmt->fetch()) {
            return $row['data'];
        }
        
        return '';
    }
    
    public function write($id, $data) {
        $expires = time() + SESSION_LIFETIME;
        
        $stmt = $this->db->prepare('
            INSERT INTO sessions (session_id, user_id, ip_address, user_agent, expires_at, data) 
            VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), ?)
            ON DUPLICATE KEY UPDATE 
                data = VALUES(data), 
                expires_at = VALUES(expires_at)
        ');
        
        $user_id = $_SESSION['user_id'] ?? null;
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        return $stmt->execute([$id, $user_id, $ip_address, $user_agent, $expires, $data]);
    }
    
    public function destroy($id) {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE session_id = ?');
        return $stmt->execute([$id]);
    }
    
    public function gc($maxlifetime) {
        $stmt = $this->db->prepare('DELETE FROM sessions WHERE expires_at < NOW()');
        return $stmt->execute();
    }
    
    public function validateSession($userId) {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $userId) {
            return false;
        }
        
        // Check if session is tied to the same IP and User Agent
        $stmt = $this->db->prepare('
            SELECT 1 FROM sessions 
            WHERE session_id = ? AND user_id = ? AND ip_address = ? AND user_agent = ?
        ');
        
        $stmt->execute([
            session_id(),
            $userId,
            $_SERVER['REMOTE_ADDR'],
            $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
        
        return (bool)$stmt->fetch();
    }
}
?>