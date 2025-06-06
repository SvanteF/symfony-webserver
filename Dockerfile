# 1. Använd officiell PHP 8.2 med Apache
FROM php:8.2-apache

# 2. Installera systemberoenden och PHP extensions
RUN apt-get update && apt-get install -y \
    unzip git libicu-dev libonig-dev libxml2-dev zip sqlite3 libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Aktivera mod_rewrite för Symfony routing
RUN a2enmod rewrite

# 4. Kopiera Composer från officiell Composer-bild
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Sätt arbetskatalog i containern
WORKDIR /var/www/html

# 6. Kopiera bara composer-filer först för att cachea dependencies
COPY composer.json composer.lock ./

# 7. Installera PHP-dependencies med Composer, tillåt plugins även som root
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction

# 8. Kopiera resten av projektfilerna
COPY . .

# 9. Ändra ägarskap på var/ om katalogen finns (cache, logs etc)
RUN if [ -d var ]; then chown -R www-data:www-data var; else echo "var directory not found, skipping chown"; fi

# 10. Sätt Apache DocumentRoot till Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 11. Ändra Apache-konfiguration för att använda ny DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 12. Exponera port 80
EXPOSE 80

# 13. Starta Apache i förgrunden
CMD ["apache2-foreground"]
