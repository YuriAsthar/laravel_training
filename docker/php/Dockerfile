FROM php:8.3-fpm-alpine3.19

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

COPY --from=composer/composer:2.8.3-bin /composer /usr/bin/composer

RUN addgroup --system --gid 1000 www
RUN adduser --uid 1000 --home /bin/bash --ingroup www www --disabled-password --system

COPY . /var/www
COPY --chown=www:www . /var/www

RUN chown -R www-data:www-data /var/www
RUN chown -R 777 /var/www

USER www

EXPOSE 9000
