# syntax=docker/dockerfile:1

FROM php:8.2-fpm-alpine AS php-base
# ctype, json, openssl, and tokenizer are enabled in the official PHP image.
# Compile only the additional extensions needed by this application.
RUN apk add --no-cache libxml2 oniguruma zstd-libs \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libxml2-dev oniguruma-dev \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring xml dom opcache \
    && apk del .build-deps
WORKDIR /var/www/html

# Install locked production dependencies before copying frequently changing source files.
FROM php-base AS vendor
COPY --from=composer/composer:2-bin /composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress \
    --no-scripts --no-autoloader
COPY . .
RUN composer dump-autoload --no-dev --classmap-authoritative --no-interaction --no-scripts \
    && php artisan package:discover --ansi

FROM php-base AS api
WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /var/www/html/vendor ./vendor
COPY deploy/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY deploy/php/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf
COPY deploy/entrypoint.sh /usr/local/bin/docker-entrypoint
RUN php artisan package:discover --ansi \
    && chmod 0555 /usr/local/bin/docker-entrypoint \
    && mkdir -p storage/framework/cache/data storage/framework/sessions \
       storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
EXPOSE 9000
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=5 \
  CMD ["php", "-r", "$s=@fsockopen('127.0.0.1',9000); exit($s?0:1);"]
ENTRYPOINT ["/usr/local/bin/docker-entrypoint"]
CMD ["php-fpm", "-F"]

# Nginx is a separate production target so it can serve HTTP while PHP-FPM stays private.
FROM nginx:alpine AS proxy
COPY deploy/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY public/ /var/www/html/public/
EXPOSE 80
HEALTHCHECK --interval=30s --timeout=5s --start-period=45s --retries=5 \
  CMD wget -q -O /dev/null http://127.0.0.1/api/products || exit 1
