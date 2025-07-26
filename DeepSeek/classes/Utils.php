<?php
class Utils {
    public static function sanitizeInput($input) {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        // Remove whitespace
        $input = trim($input);
        
        // Convert special characters to HTML entities
        $input = htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        
        return $input;
    }
    
    public static function redirect($url) {
        if (!headers_sent()) {
            header("Location: $url");
            exit();
        } else {
            echo '<script>window.location.href="' . $url . '";</script>';
            exit();
        }
    }
    
    public static function generateRandomString($length = 32) {
        return bin2hex(random_bytes($length / 2));
    }
    
    public static function validatePasswordStrength($password) {
        if (strlen($password) < 12) {
            return "Password must be at least 12 characters long";
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least one uppercase letter";
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return "Password must contain at least one lowercase letter";
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return "Password must contain at least one number";
        }
        
        if (!preg_match('/[\W]/', $password)) {
            return "Password must contain at least one special character";
        }
        
        return true;
    }
    
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        return "$protocol://$host";
    }
}
?>