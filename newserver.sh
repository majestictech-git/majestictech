#!/bin/bash

# Обновление системы
echo "Обновление системы..."
sudo apt update
sudo apt upgrade -y

# Установка необходимых компонентов
echo "Установка Apache2, MySQL и PHP..."
sudo apt install -y apache2 mysql-server php libapache2-mod-php php-mysql

# Настройка MySQL
echo "Настройка MySQL..."
sudo mysql_secure_installation <<EOF
y
0
Tenkjepy@38
Tenkjepy@38
y
y
y
y
EOF

# Создание базы данных и пользователей
echo "Создание базы данных и пользователей..."
sudo mysql -uroot -pTenkjepy@38 <<EOF
CREATE DATABASE majestictech;
CREATE USER 'Admin'@'localhost' IDENTIFIED BY 'Tenkjepy@38';
GRANT ALL PRIVILEGES ON majestictech.* TO 'Admin'@'localhost';
CREATE TABLE majestictech.users (
 id INT AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(255),
 password VARCHAR(255)
);
INSERT INTO majestictech.users (username, password) VALUES ('Artem', 'Tenkjepy@38');
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

# Создание тестовой PHP-страницы
echo "Создание тестовой PHP-страницы..."
cat <<EOF | sudo tee /var/www/html/majestictech.ru/info.php
<?php
phpinfo();
?>
EOF

# Настройка SSL-сертификата
echo "Настройка SSL-сертификата..."
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d majestictech.ru -d www.majestictech.ru
sudo systemctl restart apache2

# Настройка брандмауэра
echo "Настройка брандмауэра..."
sudo ufw allow 'Apache'
sudo ufw enable

echo "Настройка завершена. Ваш сервер готов к использованию."
echo "База данных: majestictech"
echo "Пользователь MySQL: Admin"
echo "Пароль MySQL: Tenkjepy@38"
echo "Пользователь сайта: Artem"
echo "Пароль сайта: Tenkjepy@38"
echo "Доступ к сайту: https://majestictech.ru"
echo "Тестовая PHP-страница: https://majestictech.ru/info.php"
