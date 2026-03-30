FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite

RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Passer les variables d'environnement a PHP
RUN echo 'PassEnv DB_HOST DB_NAME DB_USER DB_PASS' >> /etc/apache2/conf-available/environment.conf \
    && a2enconf environment

WORKDIR /var/www/html

EXPOSE 80
