# Use PHP 7.1 with Apache
FROM php:7.1-apache

RUN docker-php-ext-install pdo pdo_mysql

COPY --from=composer:2.2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

WORKDIR /var/www/html

COPY . /var/www/html

RUN sed -i 's#/var/www/html#/var/www/html/public#g' /etc/apache2/sites-available/000-default.conf

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

RUN composer install --no-interaction --optimize-autoloader \
    && composer dump-autoload -o

EXPOSE 80

CMD ["apache2-foreground"]
