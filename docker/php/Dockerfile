FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

RUN docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
