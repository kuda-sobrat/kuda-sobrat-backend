#!/bin/bash

# Остановить скрипт при ошибке
set -e

# Перейти в директорию приложения
cd /var/www/html

# Проверить наличие файла .env
if [ -f .env ]; then
  # Экспортировать переменные из .env в окружение
  echo "пиздец!"
  export $(grep -v '^#' .env | xargs)
else
  echo "Файл .env не найден!"
  exit 1
fi

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
