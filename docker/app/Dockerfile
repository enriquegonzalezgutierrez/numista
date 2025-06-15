# docker/app/Dockerfile

# Base image (CHANGED to PHP 8.2)
FROM php:8.2-fpm-alpine

# Set working directory
WORKDIR /var/www/html

# Add user for laravel application
# The UID will be passed as a build argument
ARG UID
RUN adduser -D -u ${UID:-1000} -s /bin/sh laravel

# Install system dependencies
# We need postgresql-dev to install the pdo_pgsql extension
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
    postgresql-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy existing application directory contents and change ownership
COPY --chown=laravel:laravel . .

# Switch to the new user
USER laravel

# Expose port 9000 and start php-fpm server
EXPOSE 9000
CMD ["php-fpm"]