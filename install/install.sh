#!/bin/bash

echo "Установка проекта анализа продаж Wildberries"

# Проверка прав root
if [ "$(id -u)" != "0" ]; then
   echo "Этот скрипт должен быть запущен с правами root" 1>&2
   exit 1
fi

# Установка зависимостей
apt update
apt install -y git nginx mysql-server php-fpm php-mysql php-curl php-gd php-mbstring php-xml php-zip php-bcmath composer nodejs npm certbot python3-certbot-nginx

# Настройка MySQL
echo "Настройка MySQL..."
mysql -e "CREATE DATABASE IF NOT EXISTS majestictech_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS 'majestictech_user'@'localhost' IDENTIFIED BY 'Tenkjepy@38';"
mysql -e "GRANT ALL PRIVILEGES ON majestictech_db.* TO 'majestictech_user'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Импорт структуры БД
echo "Импорт структуры базы данных..."
mysql majestictech_db < install/setup_db.sql

# Настройка Nginx
echo "Настройка Nginx..."
cp install/nginx.conf /etc/nginx/sites-available/majestictech.ru
ln -s /etc/nginx/sites-available/majestictech.ru /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx

# Установка SSL
echo "Установка SSL сертификата..."
certbot --nginx -d majestictech.ru --non-interactive --agree-tos -m admin@majestictech.ru

# Настройка прав
echo "Настройка прав доступа..."
chown -R www-data:www-data /var/www/majestictech.ru
chmod -R 755 /var/www/majestictech.ru
chmod -R 777 /var/www/majestictech.ru/logs
chmod -R 777 /var/www/majestictech.ru/cache
chmod -R 777 /var/www/majestictech.ru/tmp

# Установка Composer зависимостей
echo "Установка Composer зависимостей..."
cd /var/www/majestictech.ru
composer install

echo "Установка завершена!"
echo "Доступ к сайту: https://majestictech.ru"
echo "Тестовый пользователь: Artem / Tenkjepy@38"