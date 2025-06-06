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

# 6. Kopiera hela projektet
COPY . .

# 7. Installera Composer-beroenden utan att köra scripts (för att undvika .env-problem vid build)
RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 8. Ändra ägare på var/ (cache, loggar)
RUN if [ -d var ]; then chown -R www-data:www-data var; else echo "var directory not found, skipping chown"; fi

# 9. Sätt Apache DocumentRoot till Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 10. Exponera port 80
EXPOSE 80

# 11. Startkommando: kör cache-clear (förutsätter .env finns) och starta Apache i förgrunden
CMD ["bash", "-c", "php bin/console cache:clear && apache2-foreground"]
