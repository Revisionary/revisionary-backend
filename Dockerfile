FROM php:fpm


# Rewrite the PHP-FPM configurations
COPY ./config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf


# Install necessary PHP Extensions (intl for intljeremykendall/php-domain-parser, libmemcached-dev zlib1g-dev for Memcached, procps for Bg Processes)
RUN apt-get -y update \
    && apt-get install -y procps libicu-dev libmemcached-dev zlib1g-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl mysqli gd \
    && pecl install memcached-3.1.3 \
    && docker-php-ext-enable memcached


# # Expose the ports
# EXPOSE 80 443