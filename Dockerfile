# Dockerfile
FROM php:8.2-apache

# Install pdo_mysql extension
RUN docker-php-ext-install pdo_mysql

# Copy Apache configuration
COPY ./apache/symfony.conf /etc/apache2/sites-available/000-default.conf

# Set working directory
WORKDIR /var/www/html
