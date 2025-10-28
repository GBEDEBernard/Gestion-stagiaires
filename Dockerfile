# Étape 1 : Base PHP
FROM php:8.2-fpm

# Étape 2 : Dépendances système
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mbstring bcmath xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Étape 3 : Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Étape 4 : Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Étape 5 : Workdir
WORKDIR /var/www

# Étape 6 : Copier projet
COPY . .

# Étape 7 : Copier .env
COPY .env.example .env

# Étape 8 : Installer dépendances PHP (sans scripts Laravel)
RUN composer install --optimize-autoloader --no-dev --no-scripts

# Étape 9 : Installer dépendances JS et build
RUN npm install && npm run build

# Étape 10 : Permissions
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Étape 11 : Key et storage link
RUN php artisan key:generate || true
RUN php artisan storage:link || true

# Étape 12 : Port exposé
EXPOSE 8000

# Étape 13 : Commande au runtime
CMD php artisan serve --host=0.0.0.0 --port=8000
