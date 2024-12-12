FROM php:8.1-fpm-alpine

# Установка системных зависимостей
RUN apk update && apk add --no-cache \
    bash \
    netcat-openbsd \
    mysql-client \
    git \
    unzip \
    libzip-dev \
    libpq \
    oniguruma-dev \
    libxml2-dev \
    icu-dev \
    build-base \
    autoconf \
    && docker-php-ext-install pdo pdo_mysql mbstring zip pcntl intl xml

# Установка и активация расширения Redis
RUN pecl install redis \
      && docker-php-ext-enable redis

# Добавляем аргументы UID и GID
ARG UID
ARG GID

# Создаем группу и пользователя с указанными UID и GID
RUN addgroup -g ${GID} docker && \
    adduser -D -u ${UID} -G docker -s /bin/sh docker


# Установка рабочего каталога
WORKDIR /var/www/html

# Копируем файлы приложения
COPY . .

# Устанавливаем владельца файлов приложения
RUN chown -R docker:docker /var/www/html

# Настраиваем PHP-FPM для запуска под пользователем docker
RUN sed -i "s/^user = www-data/user = docker/" /usr/local/etc/php-fpm.d/www.conf && \
    sed -i "s/^group = www-data/group = docker/" /usr/local/etc/php-fpm.d/www.conf

# Переключаемся на пользователя docker **до** установки ENTRYPOINT и CMD
USER docker

# Устанавливаем права доступа для storage и bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Установка Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Установка зависимостей приложения
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Генерация ключа приложения
RUN php artisan key:generate

# Копируем скрипт entrypoint.sh
COPY --chown=docker:docker entrypoint.sh /entrypoint.sh

# Даем права на выполнение
RUN chmod +x /entrypoint.sh

# Устанавливаем скрипт в качестве точки входа
ENTRYPOINT ["/entrypoint.sh"]

# Команда запуска PHP-FPM
CMD ["php-fpm"]
