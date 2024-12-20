# Use a lightweight PHP image with CLI support
FROM php:8.2-cli

# Install necessary extensions and tools
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Set the working directory
WORKDIR /app

# Copy project files
COPY . /app

# Install Symfony dependencies
RUN composer install --no-interaction --optimize-autoloader

# Expose port for development server
EXPOSE 8000

# Default command to start the Symfony built-in server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]