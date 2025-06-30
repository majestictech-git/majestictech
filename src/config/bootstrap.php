<?php
session_start();

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../lib/Auth.php';
require_once __DIR__ . '/../lib/Logger.php';

// Настройки окружения
define('APP_ROOT', dirname(__DIR__, 2));
define('SRC_ROOT', dirname(__DIR__));
define('PUBLIC_ROOT', APP_ROOT . '/public');
define('LOG_DIR', APP_ROOT . '/logs');
define('CACHE_DIR', APP_ROOT . '/cache');
define('TMP_DIR', APP_ROOT . '/tmp');

// Автозагрузчик классов
spl_autoload_register(function ($class) {
    $file = SRC_ROOT . '/' . str_replace('\\', '/', $class) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

// Обработка ошибок
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

set_exception_handler(function ($e) {
    Logger::logError($e);
    http_response_code(500);
    echo "Произошла ошибка. Пожалуйста, попробуйте позже.";
    exit;
});

// Загрузка конфигурации
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/telegram.php';
require_once __DIR__ . '/wildberries.php';