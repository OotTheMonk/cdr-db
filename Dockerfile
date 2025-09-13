# Use the official PHP image with Apache
FROM php:8.2-apache

# Install extensions
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

# Copy app files
COPY . /var/www/html/

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80
