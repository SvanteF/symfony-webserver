# 1. Basbild med PHP 8.2 och Apache
FROM php:8.2-apache

# 2. Installera systempaket och PHP-tillägg
RUN apt-get update && apt-get install -y \
    unzip git libicu-dev libonig-dev libxml2-dev zip sqlite3 libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Aktivera Apache mod_rewrite
RUN a2enmod rewrite

# 4. Installera Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 5. Ange arbetskatalog
WORKDIR /var/www/html

# 6. Kopiera projektet (exkludera enligt .dockerignore)
COPY . .

# 7. Skapa och sätt rättigheter på var/-katalogen
RUN mkdir -p var && chown -R www-data:www-data var

# 8. Installera Composer-dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 9. Sätt Apache DocumentRoot till Symfony public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 10. Exponera port 80
EXPOSE 80

# 11. Startkommando: Skapa/migrera databas och starta Apache
CMD php bin/console doctrine:database:create --if-not-exists && \
    php bin/console doctrine:migrations:migrate --no-interaction && \
    apache2-foreground
