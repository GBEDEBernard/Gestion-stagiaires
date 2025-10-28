# Dockerfile simplifié pour PHP/Laravel
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev zip unzip git curl libonig-dev libxml2-dev nodejs npm \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring bcmath xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*


# Installe Composer

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définit le dossier de travail
WORKDIR /var/www/html

# Copie tous les fichiers du projet
COPY . .

# Autoriser le dossier comme sûr pour Git (évite les warnings)
RUN git config --global --add safe.directory /var/www/html

RUN composer install --no-dev --no-scripts --optimize-autoloader
RUN npm install && npm run build
RUN mkdir -p storage/framework/{sessions,views,cache} bootstrap/cache \
    && chmod -R 777 storage bootstrap/cache

# Génère les fichiers Laravel après l'installation
RUN php artisan key:generate --force || true && php artisan config:clear || true

EXPOSE 8080

# Production : serveur PHP intégré, dossier public comme root
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
