FROM php:8.3-fpm-alpine3.19
FROM composer:2.7.1

RUN apk update && \
    apk add \
    libpng-dev \
    jpegoptim optipng pngquant gifsicle \
    vim \
    git \
    curl \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    libexif-dev

WORKDIR /var/www

RUN docker-php-ext-install pdo pdo_pgsql
RUN docker-php-ext-install gd

COPY . /var/www
COPY --chown=www-data:www-data . /var/www/html

USER root

EXPOSE 9000

CMD ["php-fpm-alpine"]
