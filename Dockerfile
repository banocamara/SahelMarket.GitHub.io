FROM php:8.2-apache

# Installation des extensions PHP requises (PDO MySQL pour SahelMarket)
RUN docker-php-ext-install pdo pdo_mysql

# Activation du module rewrite Apache (.htaccess)
RUN a2enmod rewrite

# Copie du code source dans le conteneur
COPY . /var/www/html/

# Attribution des droits sur le répertoire Web
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80