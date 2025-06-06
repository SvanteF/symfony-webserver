# 1. Basbild med PHP 8.2 + Apache
FROM php:8.2-apache

# 2. Installera systempaket och PHP-tillägg
RUN apt-get update && apt-get install -y \
    unzip git libicu-dev libonig-dev libxml2-dev zip sqlite3 libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Aktivera Apache mod_rewrite
RUN a2enmod rewrite

# 4. Kopiera Composer från officiell Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Ange arbetskatalog
WORKDIR /var/www/html

# 6. Kopiera hela projektet (exkludera enligt .dockerignore)
COPY . .

# 7. Installera Composer dependencies utan scripts (för att undvika .env problem under build)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 8. Dump environment för produktionsmiljö (valfritt, kan kommenteras under dev)
# RUN php bin/console --env=prod cache:clear
# RUN composer dump-env prod

# 9. Ändra ägare på var/ (cache, loggar)
RUN if [ -d var ]; then chown -R www-data:www-data var; else echo "var directory not found, skipping chown"; fi

# 10. Sätt Apache DocumentRoot till Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 11. Exponera port 80
EXPOSE 80

# 12. Startkommando: Rensa cache, ägarskap, starta Apache
CMD ["bash", "-c", "php bin/console cache:clear && chown -R www-data:www-data var && apache2-foreground"]
