# Use official PHP image with Apache
FROM php:7.1-apache

# Install MySQL extension and other required extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html
RUN chmod -R 755 /var/www/html

# Create tmp directory for CSV reports
RUN mkdir -p /tmp && chmod 777 /tmp

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
