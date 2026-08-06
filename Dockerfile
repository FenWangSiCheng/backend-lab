FROM php:8.5.9-fpm-alpine

RUN docker-php-ext-install pdo_mysql

COPY --from=composer:2.10.2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

CMD ["php-fpm"]
