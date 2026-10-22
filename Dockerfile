FROM php:8.2-apache

# Install PostgreSQL extension for database connection
RUN docker-php-ext-install pdo pdo_pgsql

# Enable Apache mod_rewrite for clean URLs
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy all files to container
COPY . /var/www/html/

# Set permissions for uploads directory
RUN mkdir -p uploads && chown -R www-data:www-data /var/www/html/uploads

# Expose port 8080
EXPOSE 8080

# Configure Apache to listen on port 8080
RUN sed -i 's/Listen 80/Listen 8080/g' /etc/apache2/ports.conf
RUN sed -i 's/:80/:8080/g' /etc/apache2/sites-available/000-default.conf

# Start Apache
CMD ["apache2-foreground"]
