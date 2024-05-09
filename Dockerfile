# Use the official PHP 8 image
FROM php:8.0

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql zip opcache

# Enable OPCache
RUN docker-php-ext-enable opcache

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Enable JIT
RUN docker-php-ext-enable opcache --ini-name=z-opcache.ini \
    && echo "opcache.jit_buffer_size=100M" >> /usr/local/etc/php/conf.d/z-opcache.ini \
    && echo "opcache.jit=tracing" >> /usr/local/etc/php/conf.d/z-opcache.ini \
    && echo "opcache.jit_debug=0" >> /usr/local/etc/php/conf.d/z-opcache.ini \
    && echo "opcache.enable_cli=1" >> /usr/local/etc/php/conf.d/z-opcache.ini

# Install and configure Swoole
RUN pecl install swoole \
    && docker-php-ext-enable swoole

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

COPY .env .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Cache Laravel configs and routes 
RUN php artisan optimize

# Expose ports
EXPOSE 8000

# Start Laravel Octane with Swoole
CMD ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8000", "--workers=auto"]
