FROM php:8.2-fpm

# ---- Installer les dépendances système ----
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip unzip git curl libonig-dev \
    && docker-php-ext-configure gd \
        --with-freetype=/usr/include/ \
        --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd pdo pdo_mysql mbstring bcmath xml

# ---- Installer Composer ----
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# ---- Installer Node.js (pour Vite ou Mix) ----
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs

# ---- Copier le projet ----
WORKDIR /var/www/html
COPY . .

# ---- Installer les dépendances ----
RUN composer install --optimize-autoloader --no-dev
RUN npm install
RUN npm run build

# ---- Gérer les permissions Laravel ----
RUN mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# ---- Générer clé & lien storage ----
RUN php artisan key:generate || true
RUN php artisan storage:link || true

# ---- Exposer le port utilisé ----
EXPOSE 8080

# ---- Lancer l’application ----
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
