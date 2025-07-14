#utiliser une img php officielle avec Apache
FROM php:8.2-apache

#installer les dependances & biblios
RUN apt-get update && apt-get install -y && apt-get update && apt-get install -y --no-install-recommends \
    libzip-dev \
    unzip \
    && docker-php-ext-install pdo pdo_mysql zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

#activer rewrite sur apache pour les url \
RUN a2enmod mod_rewrite

#installer composer
COPY --from=composer:latest /user/bin/composer /user/bin/composer

#definir le repertoire de travail
WORKDIR /var/www/html

#copier fichier de dependance et les installer
COPY composer.json composer.lock ./
RUN compser install --no-interaction --non-plugins --no-scripts --prefer-dist

#copier du reste du code de l'app
COPY . .

#executer le dump de l'autoloader de composer(pour les perfs)
RUN composer dump-autoload --optimize

#changer le proprietaire des fichiers pour donné le droit au serv d'ecrire dans les fichiers (ex: logs)
RUN mkdir -p storage/Logs && \
    chown -R www-data:www-data /var/www/html/storage