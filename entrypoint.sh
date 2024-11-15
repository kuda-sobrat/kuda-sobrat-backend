#!/bin/bash

# Остановить скрипт при ошибке
set -e

# Ждем доступности базы данных
echo "Ожидание доступности базы данных на $DB_HOST:$DB_PORT..."

# Проверяем доступность базы данных
while ! nc -z $DB_HOST $DB_PORT; do
  sleep 1
  echo "База данных недоступна, повторяем попытку... $DB_HOST:$DB_PORT"
done

echo "База данных доступна, продолжаем выполнение..."

# Очистка кэша конфигурации Laravel
php artisan config:clear

# Выполнение миграций
echo "Выполнение миграций..."
php artisan migrate --force
echo "Запуск сидеров..."
php artisan db:seed

# Запуск основного процесса (PHP-FPM)
exec "$@"
