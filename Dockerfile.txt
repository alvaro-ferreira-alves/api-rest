FROM php:7.4-apache
COPY . /var/www/html

# Install PostgreSQL dependencies
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PostgreSQL extension for PHP
RUN docker-php-ext-install pdo pdo_pgsql

# Copy your web application code to the container
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

# Configure Apache
RUN a2enmod rewrite

# Expose the port
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]