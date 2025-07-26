<?php
class Auth {
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
    }
    
    public function register($username, $email, $password) {
        // Validate input
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        if (strlen($username) < 3 || strlen($username) > 50) {
            throw new Exception("Username must be between 3 and 50 characters");
        }
        
        if (strlen($password) < 12) {
            throw new Exception("Password must be at least 12 characters long");
        }
        
        // Check if username or email already exists
        $stmt = $this->db->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            throw new Exception("Username or email already exists");
        }
        
        // Hash password with pepper
        $passwordHash = password_hash($password . PEPPER, PASSWORD_ARGON2ID);
        
        // Insert new user
        $stmt = $this->db->prepare('
            INSERT INTO users (username, email, password_hash) 
            VALUES (?, ?, ?)
        ');
        
        $stmt->execute([$username, $email, $passwordHash]);
        
        return $this->db->lastInsertId();
    }
    
    public function login($username, $password) {
        // Check for brute force
        $this->checkBruteForce($username);
        
        // Get user data
        $stmt = $this->db->prepare('
            SELECT id, username, password_hash, account_locked, lockout_until 
            FROM users 
            WHERE username = ?
        ');
        $stmt->execute([$username]);
        
        $user = $stmt->fetch();
        
        if (!$user) {
            $this->recordFailedAttempt($username);
            throw new Exception("Invalid username or password");
        }
        
        // Check if account is locked
        if ($user['account_locked'] || 
            ($user['lockout_until'] && strtotime($user['lockout_until']) > time())) {
            throw new Exception("Account is temporarily locked due to too many failed attempts");
        }
        
        // Verify password
        if (password_verify($password . PEPPER, $user['password_hash'])) {
            // Password is correct, reset failed attempts
            $this->resetFailedAttempts($user['id']);
            
            // Update last login
            $stmt = $this->db->prepare('
                UPDATE users 
                SET last_login = NOW() 
                WHERE id = ?
            ');
            $stmt->execute([$user['id']]);
            
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['logged_in'] = true;
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
            
            // Regenerate session ID to prevent fixation
            session_regenerate_id(true);
            
            return true;
        } else {
            // Password is incorrect, record failed attempt
            $this->recordFailedAttempt($user['id']);
            throw new Exception("Invalid username or password");
        }
    }
    
    public function logout() {
        // Unset all session variables
        $_SESSION = [];
        
        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $stmt = $this->db->prepare('SELECT id, username, email FROM users WHERE id = ?');
        $stmt->execute([$_SESSION['user_id']]);
        
        return $stmt->fetch();
    }
    
    private function checkBruteForce($username) {
        $stmt = $this->db->prepare('
            SELECT COUNT(*) AS attempts 
            FROM login_attempts 
            WHERE username = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
        ');
        $stmt->execute([$username]);
        
        $result = $stmt->fetch();
        
        if ($result['attempts'] >= MAX_LOGIN_ATTEMPTS) {
            throw new Exception("Too many login attempts. Please try again later.");
        }
    }
    
    private function recordFailedAttempt($userId) {
        // Increment failed attempts counter
        $stmt = $this->db->prepare('
            UPDATE users 
            SET failed_login_attempts = failed_login_attempts + 1 
            WHERE id = ?
        ');
        $stmt->execute([$userId]);
        
        // Check if account should be locked
        $stmt = $this->db->prepare('
            SELECT failed_login_attempts 
            FROM users 
            WHERE id = ?
        ');
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        
        if ($result['failed_login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $lockoutUntil = date('Y-m-d H:i:s', time() + LOCKOUT_TIME);
            $stmt = $this->db->prepare('
                UPDATE users 
                SET account_locked = TRUE, lockout_until = ? 
                WHERE id = ?
            ');
            $stmt->execute([$lockoutUntil, $userId]);
        }
    }
    
    private function resetFailedAttempts($userId) {
        $stmt = $this->db->prepare('
            UPDATE users 
            SET failed_login_attempts = 0, account_locked = FALSE, lockout_until = NULL 
            WHERE id = ?
        ');
        $stmt->execute([$userId]);
    }
    
    public function generateCSRFToken() {
        if (!$this->isLoggedIn()) {
            throw new Exception("User not logged in");
        }
        
        $token = bin2hex(random_bytes(64));
        $expiresAt = date('Y-m-d H:i:s', time() + CSRF_TOKEN_LIFETIME);
        
        $stmt = $this->db->prepare('
            INSERT INTO csrf_tokens (token, user_id, expires_at) 
            VALUES (?, ?, ?)
        ');
        $stmt->execute([$token, $_SESSION['user_id'], $expiresAt]);
        
        return $token;
    }
    
    public function validateCSRFToken($token) {
        if (!$this->isLoggedIn() || empty($token)) {
            return false;
        }
        
        // Delete expired tokens first
        $this->db->exec('DELETE FROM csrf_tokens WHERE expires_at < NOW()');
        
        $stmt = $this->db->prepare('
            DELETE FROM csrf_tokens 
            WHERE token = ? AND user_id = ? AND expires_at >= NOW()
        ');
        $stmt->execute([$token, $_SESSION['user_id']]);
        
        return $stmt->rowCount() > 0;
    }
}
?>