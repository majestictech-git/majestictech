<?php
namespace MajesticTech\Models;

use MajesticTech\Lib\Database;

class WildberriesAPI {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getAPIKey($userId) {
        $result = $this->db->query(
            "SELECT api_key FROM wildberries_api WHERE user_id = ? AND is_active = TRUE ORDER BY created_at DESC LIMIT 1",
            [$userId]
        );
        
        return $result->num_rows > 0 ? $result->fetch_assoc()['api_key'] : null;
    }
    
    public function saveAPIKey($userId, $apiKey) {
        // Деактивируем все предыдущие ключи
        $this->db->update(
            'wildberries_api',
            ['is_active' => FALSE],
            ['user_id' => $userId]
        );
        
        // Сохраняем новый ключ
        $this->db->insert('wildberries_api', [
            'user_id' => $userId,
            'api_key' => $apiKey,
            'is_active' => TRUE
        ]);
        
        return true;
    }
    
    public function getSalesData($apiKey, $dateFrom, $dateTo) {
        $url = "https://suppliers-api.wildberries.ru/api/v1/supplier/sales?dateFrom=" . urlencode($dateFrom) . "&dateTo=" . urlencode($dateTo);
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка API Wildberries: HTTP код $httpCode. Ответ: $response");
        }
        
        return json_decode($response, true);
    }
    
    public function getStocksData($apiKey) {
        $url = "https://suppliers-api.wildberries.ru/api/v1/stocks";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . $apiKey,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new \Exception("Ошибка API Wildberries: HTTP код $httpCode. Ответ: $response");
        }
        
        return json_decode($response, true);
    }
}