FROM php:7.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
&& docker-nginx-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
&& docker-nginx-ext-install gd pdo pdo_mysql mysqli

WORKDIR /var/www/html
