<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MajesticTech - Анализ продаж Wildberries</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="page-wrapper">
        <header class="main-header">
            <div class="container">
                <div class="header-content">
                    <div class="logo">
                        <a href="/dashboard">
                            <img src="/assets/images/logo.png" alt="MajesticTech">
                            <span>Wildberries Analytics</span>
                        </a>
                    </div>
                    <nav class="main-nav">
                        <ul>
                            <li><a href="/dashboard"><i class="fas fa-home"></i> Дашборд</a></li>
                            <li><a href="/settings"><i class="fas fa-cog"></i> Настройки</a></li>
                            <?php if ($auth->isAdmin()): ?>
                                <li><a href="/admin"><i class="fas fa-lock"></i> Админка</a></li>
                            <?php endif; ?>
                            <li><a href="/logout"><i class="fas fa-sign-out-alt"></i> Выход</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
        </header>

        <div class="content-wrapper">
            <aside class="sidebar">
                <div class="user-profile">
                    <div class="avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-info">
                        <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'Гость') ?></span>
                        <span class="role"><?= htmlspecialchars($_SESSION['role'] ?? 'guest') ?></span>
                    </div>
                </div>
                
                <nav class="sidebar-nav">
                    <ul>
                        <li>
                            <a href="/dashboard">
                                <i class="fas fa-chart-line"></i>
                                <span>Аналитика продаж</span>
                            </a>
                        </li>
                        <li>
                            <a href="/stocks">
                                <i class="fas fa-boxes"></i>
                                <span>Остатки на складах</span>
                            </a>
                        </li>
                        <li>
                            <a href="/orders">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Заказы</span>
                            </a>
                        </li>
                        <li>
                            <a href="/reports">
                                <i class="fas fa-file-alt"></i>
                                <span>Отчеты</span>
                            </a>
                        </li>
                        <li>
                            <a href="/settings">
                                <i class="fas fa-cog"></i>
                                <span>Настройки</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </aside>

            <main class="main-content">
                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_SESSION['success_message']) ?>
                        <?php unset($_SESSION['success_message']); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error_message'])): ?>
                    <div class="alert alert-error">
                        <?= htmlspecialchars($_SESSION['error_message']) ?>
                        <?php unset($_SESSION['error_message']); ?>
                    </div>
                <?php endif; ?>