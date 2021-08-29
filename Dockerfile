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
    --no-dev \
    --prefer-dist



#
# Application
#
FROM php:7.4-fpm

# Rewrite the PHP-FPM configurations
COPY ./config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Install necessary PHP Extensions (intl for intljeremykendall/php-domain-parser, libmemcached-dev zlib1g-dev for Memcached, procps for Bg Processes)
RUN apt-get -y update \
    && apt-get install -y procps libicu-dev libmemcached-dev zlib1g-dev libjpeg62-turbo-dev libpng-dev libfreetype6-dev nginx \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install intl mysqli gd \
    && pecl install memcached-3.1.3 \
    && docker-php-ext-enable memcached

# Default Arguments
ARG API_SUBDOMAIN=dpi
ARG API_INSECURE_SUBDOMAIN=dpi
ARG API_DOMAIN=revisionary.co

# Nginx Template
ARG NGINX_FILE="/etc/nginx/sites-enabled/default"
COPY ./config/nginx/revisionary.conf.template ${NGINX_FILE}
RUN sed -i "s/\${SITES_SUBDOMAIN}/${SITES_SUBDOMAIN}/g" "${NGINX_FILE}"
RUN sed -i "s/\${API_SUBDOMAIN}/${API_SUBDOMAIN}/g" "${NGINX_FILE}"
RUN sed -i "s/\${API_INSECURE_SUBDOMAIN}/${API_INSECURE_SUBDOMAIN}/g" "${NGINX_FILE}"
RUN sed -i "s/\${API_DOMAIN}/${API_DOMAIN}/g" "${NGINX_FILE}"

# Forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log
RUN ln -sf /dev/stderr /var/log/nginx/error.log

# Add the files
ADD ./src /backend
COPY --from=vendor /app/vendor /backend/vendor

# Update the permissions
RUN chown -R www-data:www-data /backend/ \
    && find /backend/ -type f -exec chmod 644 {} \; \
    && find /backend/ -type d -exec chmod 755 {} \;

WORKDIR /backend

EXPOSE 80 443
CMD php-fpm | nginx -g 'daemon off;'
