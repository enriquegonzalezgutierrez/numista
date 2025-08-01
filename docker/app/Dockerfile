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
    npm \
    bash \
    jq

# Install Stripe CLI
ARG STRIPE_CLI_VERSION=1.19.4
RUN ARCH=$(uname -m) && \
    case ${ARCH} in \
        "x86_64") STRIPE_ARCH="x86_64" ;; \
        "amd64") STRIPE_ARCH="x86_64" ;; \
        "aarch64") STRIPE_ARCH="arm64" ;; \
        *) STRIPE_ARCH=${ARCH} ;; \
    esac && \
    STRIPE_CLI_URL=$(curl -s https://api.github.com/repos/stripe/stripe-cli/releases/latest \
    | jq -r ".assets[] | select(.name | test(\"linux_${STRIPE_ARCH}.tar.gz\")) | .browser_download_url") && \
    curl -L "${STRIPE_CLI_URL}" -o stripe.tar.gz && \
    tar -xf stripe.tar.gz && \
    mv stripe /usr/local/bin/stripe && \
    rm stripe.tar.gz

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo pdo_pgsql zip intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create necessary directories for Supervisor
RUN mkdir -p /var/log/supervisor /etc/supervisor/conf.d

# Copy supervisor configuration
COPY docker/app/supervisor/supervisord.conf /etc/supervisor/supervisord.conf
COPY docker/app/supervisor/php-fpm.conf /etc/supervisor/conf.d/
COPY docker/app/supervisor/laravel-worker.conf /etc/supervisor/conf.d/
COPY docker/app/supervisor/stripe-webhook.conf /etc/supervisor/conf.d/

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

# Create log directories and files needed by supervisor programs to prevent startup failure.
RUN mkdir -p /var/www/html/storage/logs
RUN touch /var/www/html/storage/logs/worker.log /var/www/html/storage/logs/stripe.log
RUN chown -R laravel:www-data /var/www/html/storage/logs

# Copy the entrypoint script and make it executable
COPY docker/app/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 9000 for php-fpm
EXPOSE 9000

# Use the entrypoint script to start the container's main process
ENTRYPOINT ["entrypoint.sh"]