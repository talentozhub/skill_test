# Start from the official PHP 7.1 FPM image
FROM php:7.1-fpm

# Install the required PHP extensions (for the refactored code)
RUN docker-php-ext-install pdo_mysql mysqli

WORKDIR /var/www/html