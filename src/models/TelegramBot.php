<?php
namespace MajesticTech\Models;

use MajesticTech\Lib\Database;

class TelegramBot {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getBotSettings($userId) {
        $result = $this->db->query(
            "SELECT bot_token, chat_id, notifications_enabled FROM telegram_settings WHERE user_id = ?",
            [$userId]
        );
        
        return $result->num_rows > 0 ? $result->fetch_assoc() : null;
    }
    
    public function saveBotSettings($userId, $botToken, $chatId = null, $notificationsEnabled = true) {
        $existingSettings = $this->getBotSettings($userId);
        
        if ($existingSettings) {
            $this->db->update(
                'telegram_settings',
                [
                    'bot_token' => $botToken,
                    'chat_id' => $chatId,
                    'notifications_enabled' => $notificationsEnabled
                ],
                ['user_id' => $userId]
            );
        } else {
            $this->db->insert('telegram_settings', [
                'user_id' => $userId,
                'bot_token' => $botToken,
                'chat_id' => $chatId,
                'notifications_enabled' => $notificationsEnabled
            ]);
        }
        
        return true;
    }
    
    public function sendMessage($botToken, $chatId, $message) {
        $url = "https://api.telegram.org/bot{$botToken}/sendMessage";
        
        $data = [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'HTML'
        ];
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка отправки сообщения в Telegram: HTTP код $httpCode. Ответ: $response");
        }
        
        return json_decode($response, true);
    }
    
    public function setWebhook($botToken, $webhookUrl) {
        $url = "https://api.telegram.org/bot{$botToken}/setWebhook?url={$webhookUrl}";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка установки вебхука: HTTP код $httpCode. Ответ: $response");
        }
        
        return json_decode($response, true);
    }
}