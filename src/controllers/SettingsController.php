<?php
namespace MajesticTech\Controllers;

use MajesticTech\Lib\Auth;
use MajesticTech\Models\WildberriesAPI;
use MajesticTech\Models\TelegramBot;

class SettingsController {
    private $auth;
    private $wbAPI;
    private $telegramBot;
    
    public function __construct() {
        $this->auth = new Auth();
        $this->wbAPI = new WildberriesAPI();
        $this->telegramBot = new TelegramBot();
        
        if (!$this->auth->isLoggedIn()) {
            header('Location: /login');
            exit;
        }
    }
    
    public function index() {
        $user = $this->auth->getUser();
        $wbApiKey = $this->wbAPI->getAPIKey($user['id']);
        $telegramSettings = $this->telegramBot->getBotSettings($user['id']);
        
        require __DIR__ . '/../views/settings.php';
    }
    
    public function saveWBSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /settings');
            exit;
        }
        
        $user = $this->auth->getUser();
        $apiKey = $_POST['wb_api_key'] ?? '';
        
        if (!empty($apiKey)) {
            $this->wbAPI->saveAPIKey($user['id'], $apiKey);
            $_SESSION['success_message'] = "API ключ Wildberries успешно сохранен!";
        }
        
        header('Location: /settings');
        exit;
    }
    
    public function saveTelegramSettings() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /settings');
            exit;
        }
        
        $user = $this->auth->getUser();
        $botToken = $_POST['bot_token'] ?? '';
        $chatId = $_POST['chat_id'] ?? '';
        $notificationsEnabled = isset($_POST['notifications_enabled']);
        
        if (!empty($botToken)) {
            $this->telegramBot->saveBotSettings($user['id'], $botToken, $chatId, $notificationsEnabled);
            $_SESSION['success_message'] = "Настройки Telegram бота успешно сохранены!";
        }
        
        header('Location: /settings');
        exit;
    }
    
    public function testTelegram() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /settings');
            exit;
        }
        
        $user = $this->auth->getUser();
        $telegramSettings = $this->telegramBot->getBotSettings($user['id']);
        
        if (!$telegramSettings || empty($telegramSettings['chat_id'])) {
            $_SESSION['error_message'] = "Не настроен chat_id для Telegram бота";
            header('Location: /settings');
            exit;
        }
        
        try {
            $this->telegramBot->sendMessage(
                $telegramSettings['bot_token'],
                $telegramSettings['chat_id'],
                "Тестовое сообщение от MajesticTech\n\nЭто сообщение подтверждает, что настройки Telegram бота работают корректно."
            );
            
            $_SESSION['success_message'] = "Тестовое сообщение успешно отправлено!";
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Ошибка отправки сообщения: " . $e->getMessage();
        }
        
        header('Location: /settings');
        exit;
    }
}