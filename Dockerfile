# 1. Basbild: PHP 8.2 med Apache
FROM php:8.2-apache

# 2. Installera systempaket och PHP-tillägg som behövs
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    libicu-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install intl pdo pdo_sqlite xml opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Aktivera Apache mod_rewrite för Symfony-routes
RUN a2enmod rewrite

# 4. Kopiera composer från officiell composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 5. Ange arbetskatalog
WORKDIR /var/www/html

# 6. Kopiera först bara composer.json och composer.lock för caching
COPY composer.json composer.lock ./

# 7. Kör composer install för att hämta dependencies tidigt (för caching)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 8. Kopiera resten av koden
COPY . .

# 9. Kör scripts (om du vill, annars kan du ta bort detta)
RUN composer run-script post-install-cmd

# 10. Ge rättigheter till var/ om den finns
RUN if [ -d var ]; then chown -R www-data:www-data var; else echo "var directory not found, skipping chown"; fi

# 11. Sätt Apache DocumentRoot till public/
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# 12. Ändra Apache-konfig så att DocumentRoot pekar på public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/000-default.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# 13. Exponera port 80
EXPOSE 80

# 14. Starta apache i förgrunden
CMD ["apache2-foreground"]
