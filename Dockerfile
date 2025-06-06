# 1. Använd officiell PHP-bild med Apache
FROM php:8.2-apache

# 2. Installera systemberoenden och PHP-tillägg
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_mysql pdo_sqlite xml opcache

# 3. Aktivera Apache mod_rewrite
RUN a2enmod rewrite

# 4. Installera Composer globalt
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Ange arbetskatalog
WORKDIR /var/www/html

# 6. Kopiera projektet till containern
COPY . .

# 7. Installera PHP-bibliotek utan dev och utan auto-scripts
RUN composer install --no-dev --optimize-autoloader --no-scripts

# 8. Ange public/ som DocumentRoot i Apache
RUN sed -i 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 9. Öppna port 80
EXPOSE 80

# 10. Starta Apache i förgrunden
CMD ["apache2-foreground"]
