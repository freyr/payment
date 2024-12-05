FROM php:8.4-cli-alpine3.20 AS os
ENV COMPOSER_ALLOW_SUPERUSER=1
COPY --from=composer/composer:2-bin /composer /usr/bin/composer

WORKDIR /app