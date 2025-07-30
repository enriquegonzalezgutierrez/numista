# docker/app/Dockerfile

# Base image (PHP 8.2)
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Add user for laravel application
ARG UID
RUN adduser -D -u ${UID:-1000} -s /bin/sh laravel

# Install system dependencies
RUN apk add --no-cache \
    build-base \
    nginx \
    supervisor \
    curl \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    postgresql-dev \
    icu-dev \
    icu-data-full \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy supervisor config
COPY docker/app/supervisor/supervisord.conf /etc/supervisord.conf
COPY docker/app/supervisor/laravel-worker.conf /etc/supervisor/conf.d/laravel-worker.conf
COPY docker/app/supervisor/php-fpm.conf /etc/supervisor/conf.d/php-fpm.conf

# THE FIX: Simplify Dockerfile. Only copy files and install dependencies.
# Laravel optimizations will be handled by Makefile after the container is running.

# Set user to laravel for npm install to avoid permission issues
USER laravel

# Copy dependency files and install them
COPY --chown=laravel:laravel package.json package-lock.json ./
RUN npm ci

# Copy the rest of the application files
COPY --chown=laravel:laravel . .

# Install composer dependencies
RUN composer install --optimize-autoloader --no-scripts

# Build frontend assets
RUN npm run build

# Switch back to root to set final permissions
USER root

# Ensure storage and bootstrap/cache are writable by the server and user
RUN chown -R laravel:www-data /var/www/html/storage /var/www/html/bootstrap/cache
RUN chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Switch to the final user
USER laravel

# Expose port 9000
EXPOSE 9000

# Final command to start supervisord
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]