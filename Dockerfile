FROM php:8.4-apache

# 1. Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN a2enmod rewrite

# 2. Setup Apache Document Root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# 3. OPTIMIZATION: Copy only composer files first to cache dependencies
COPY composer.json composer.lock ./

# 4. Install dependencies 
# NOTE: You MUST move l5-swagger to "require" in composer.json for this to work with --no-dev
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# 5. Copy the rest of the application
COPY . .

# 6. Generate autoloader and run Laravel package discovery
RUN composer dump-autoload --optimize

# 7. Generate swagger docs
# We pass dummy environment variables in case your config requires them to boot during the build
RUN APP_ENV=production APP_KEY=base64:$(head -c 32 /dev/urandom | base64) php artisan l5-swagger:generate

# 8. Set permissions LAST, so all generated files (like swagger docs) get the correct owner
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache