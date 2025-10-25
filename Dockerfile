# Official PHP image with Apache
FROM php:7.1.33-apache

# Required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

# Copy project files into the container
COPY . /var/www/html/

# Working directory
WORKDIR /var/www/html

# Expose port 80
EXPOSE 80

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Start Apache in the foreground
CMD ["apache2-foreground"]
