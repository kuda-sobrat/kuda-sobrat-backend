FROM php:8.1-fpm-alpine

# Установка системных зависимостей
RUN apk update && apk add --no-cache \
    build-base \
    autoconf \
    git \
    unzip \
    libpq \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# Установка и активация расширения Redis
RUN pecl install redis \
      && docker-php-ext-enable redis

# Установка рабочего каталога
WORKDIR /var/www/html

# Копируем файлы приложения
COPY . .

RUN cp .env.example .env

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей приложения
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Генерация ключа приложения
RUN php artisan key:generate

# Настройка прав доступа
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Команда запуска PHP-FPM
CMD ["php-fpm"]
