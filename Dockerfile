FROM php:8.0-fpm

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libjpeg-dev libfreetype6-dev \
    libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy project files
COPY . .

# Create a temporary .env so artisan commands don't fail during build
RUN cp .env.example .env || true

# Install dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Generate app key
RUN php artisan key:generate

# Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]

