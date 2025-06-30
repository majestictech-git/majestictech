<?php
namespace MajesticTech\Lib;

class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function login($username, $password) {
        $result = $this->db->query(
            "SELECT * FROM users WHERE username = ?", 
            [$username]
        );
        
        if ($result->num_rows === 0) {
            return false;
        }
        
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            Logger::logAction($user['id'], 'login', 'Успешный вход в систему');
            return true;
        }
        
        Logger::logAction(null, 'failed_login', "Неудачная попытка входа для пользователя $username");
        return false;
    }
    
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            Logger::logAction($_SESSION['user_id'], 'logout', 'Выход из системы');
        }
        
        session_unset();
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function isAdmin() {
        return $this->isLoggedIn() && $_SESSION['role'] === 'admin';
    }
    
    public function getUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $result = $this->db->query(
            "SELECT id, username, email, role, created_at FROM users WHERE id = ?", 
            [$_SESSION['user_id']]
        );
        
        return $result->fetch_assoc();
    }
    
    public function register($username, $password, $email, $role = 'user') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $userId = $this->db->insert('users', [
                'username' => $username,
                'password' => $hashedPassword,
                'email' => $email,
                'role' => $role
            ]);
            
            Logger::logAction($userId, 'register', 'Новый пользователь зарегистрирован');
            return $userId;
        } catch (\Exception $e) {
            Logger::logError($e);
            return false;
        }
    }
}