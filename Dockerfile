#
# PHP Dependencies
#
FROM composer:latest as vendor

COPY ./src/composer.json composer.json
COPY ./src/composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist



# #
# # Frontend
# #
# FROM node:8.11 as frontend

# RUN mkdir -p /app/public

# COPY package.json webpack.mix.js yarn.lock /app/
# COPY resources/assets/ /app/resources/assets/

# WORKDIR /app

# RUN yarn install && yarn production



#
# Application
#
FROM php:7.4-apache

# Add the self signed certificates
COPY ./certificates/revisionaryapp.com.crt /etc/apache2/ssl/revisionaryapp.com.crt
COPY ./certificates/revisionaryapp.com.key /etc/apache2/ssl/revisionaryapp.com.key

# Rewrite the Apache and PHP configurations
COPY ./config/apache-ssl.conf /etc/apache2/sites-enabled/apache-ssl.conf
COPY ./config/php.ini /usr/local/etc/php/

# Add the server name to Apache configuration
RUN echo "ServerName revisionaryapp.com" >> /etc/apache2/apache2.conf

# Install necessary PHP Extensions (intl for intljeremykendall/php-domain-parser, libmemcached-dev zlib1g-dev for Memcached, procps for Bg Processes)
RUN apt-get -y update \
    && apt-get install -y procps libicu-dev libmemcached-dev zlib1g-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl mysqli gd \
    && pecl install memcached-3.1.3 \
    && docker-php-ext-enable memcached

# Activate the rewrite engine and SSL
RUN a2enmod rewrite
RUN a2enmod ssl
RUN service apache2 restart

# Add the project files
ADD ./src /var/www/html
COPY --from=vendor /app/vendor /var/www/html/vendor
#ADD ./logs /var/www/logs
#ADD ./sessions /var/www/sessions

# Update the permissions
RUN chown -R www-data:www-data /var/www/ \
    && find /var/www/ -type f -exec chmod 644 {} \; \
    && find /var/www/ -type d -exec chmod 755 {} \;