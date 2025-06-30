<?php
namespace MajesticTech\Controllers;

use MajesticTech\Lib\Auth;

class AuthController {
    private $auth;
    
    public function __construct() {
        $this->auth = new Auth();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->auth->login($username, $password)) {
                header('Location: /dashboard');
                exit;
            } else {
                $error = "Неверное имя пользователя или пароль";
                require __DIR__ . '/../views/auth/login.php';
                exit;
            }
        }
        
        require __DIR__ . '/../views/auth/login.php';
    }
    
    public function logout() {
        $this->auth->logout();
        header('Location: /login');
        exit;
    }
    
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $email = $_POST['email'] ?? '';
            
            if ($this->auth->register($username, $password, $email)) {
                header('Location: /login');
                exit;
            } else {
                $error = "Ошибка регистрации. Возможно, пользователь уже существует.";
            }
        }
        
        require __DIR__ . '/../views/auth/register.php';
    }
}