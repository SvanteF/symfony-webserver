# 1. Använd officiell PHP 8.2-bild med Apache
FROM php:8.2-apache

# 2. Installera systemberoenden och PHP extensions
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite xml opcache

# 3. Aktivera mod_rewrite för Symfony routing
RUN a2enmod rewrite

# 4. Kopiera Composer från Composer-bilden
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Ange arbetskatalog
WORKDIR /var/www/html

# 6. Kopiera alla filer in i containern
COPY . .

# 7. Installera PHP-dependencies, hoppa över dev-paket
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 8. (Valfritt men rekommenderat) Skapa rättigheter till var/cache/logs
RUN if [ -d var ]; then chown -R www-data:www-data var; else echo "var directory not found, skipping chown"; fi

# 9. Ställ in Apache DocumentRoot till Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 10. Justera Apache-konfig för ny DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/000-default.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 11. Exponera porten
EXPOSE 80

# 12. Kör Apache i förgrunden
CMD ["apache2-foreground"]
