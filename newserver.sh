#!/bin/bash

# Обновление системы
echo "Обновление системы..."
sudo apt update
sudo apt upgrade -y

# Установка необходимых компонентов
echo "Установка Apache2, MySQL и PHP..."
sudo apt install -y apache2 mysql-server php php-curl php-json certbot python3-certbot-apache libapache2-mod-php php-mysql

# Настройка MySQL
echo "Настройка MySQL..."
sudo mysql_secure_installation <<EOF
y
Tenkjepy@38
Tenkjepy@38
y
y
y
y
EOF

# Создание базы данных для сайта
echo "Создание базы данных для сайта..."
sudo mysql -uroot -pTenkjepy@38 <<EOF
CREATE DATABASE majestictech;
GRANT ALL PRIVILEGES ON majestictech.* TO 'admin'@'localhost' IDENTIFIED BY 'Admin123!';
FLUSH PRIVILEGES;
EOF

# Настройка Apache
echo "Настройка Apache..."
sudo a2enmod rewrite
sudo systemctl restart apache2

# Создание виртуального хоста
echo "Создание виртуального хоста..."
cat <<EOF | sudo tee /etc/apache2/sites-available/majestictech.ru.conf
<VirtualHost *:80>
    ServerAdmin webmaster@majestictech.ru
    ServerName majestictech.ru
    ServerAlias www.majestictech.ru
    DocumentRoot /var/www/html/majestictech.ru
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
    <Directory /var/www/html/majestictech.ru>
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
EOF

# Активация виртуального хоста
sudo a2ensite majestictech.ru.conf
sudo systemctl restart apache2

# Создание директории для сайта
echo "Создание директории для сайта..."
sudo mkdir -p /var/www/html/majestictech.ru
sudo chown -R www-data:www-data /var/www/html/majestictech.ru
sudo chmod -R 755 /var/www/html

# Тестовая страница
echo "Создание тестовой страницы..."
cat <<EOF | sudo tee /var/www/html/majestictech.ru/info.php
<?php
phpinfo();
?>
EOF

# Настройка брандмауэра
echo "Настройка брандмауэра..."
sudo ufw allow 'Apache'
sudo ufw enable

echo "Настройка завершена. Ваш сервер готов к использованию."
echo "База данных: majestictech"
echo "Пользователь MySQL: admin"
echo "Пароль MySQL: Admin123!"
echo "Доступ к сайту: http://majestictech.ru"
