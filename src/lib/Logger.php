<?php
namespace MajesticTech\Lib;

class Logger {
    public static function logAction($userId, $action, $details = '') {
        $db = Database::getInstance();
        
        try {
            $db->insert('logs', [
                'user_id' => $userId,
                'action' => $action,
                'details' => $details,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (\Exception $e) {
            error_log("Ошибка логирования: " . $e->getMessage());
        }
        
        // Дополнительное логирование в файл
        $logMessage = sprintf(
            "[%s] UserID: %s | Action: %s | Details: %s | IP: %s | UA: %s\n",
            date('Y-m-d H:i:s'),
            $userId ?? 'guest',
            $action,
            $details,
            $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        );
        
        file_put_contents(LOG_DIR . '/actions.log', $logMessage, FILE_APPEND);
    }
    
    public static function logError(\Throwable $e) {
        $logMessage = sprintf(
            "[%s] ERROR: %s in %s on line %d\nStack trace:\n%s\n",
            date('Y-m-d H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $e->getTraceAsString()
        );
        
        file_put_contents(LOG_DIR . '/errors.log', $logMessage, FILE_APPEND);
        
        // Логирование в базу данных, если пользователь авторизован
        if (isset($_SESSION['user_id'])) {
            self::logAction(
                $_SESSION['user_id'],
                'error',
                $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine()
            );
        }
    }
}