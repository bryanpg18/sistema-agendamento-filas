FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM serversideup/php:8.4-fpm-nginx AS app

USER root
WORKDIR /var/www/html

COPY . .
COPY --from=frontend /app/public/build ./public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache

COPY entrypoint.d/60-run-migrations.sh /etc/entrypoint.d/60-run-migrations.sh

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stderr \
    NGINX_WEBROOT=/var/www/html/public \
    HEALTHCHECK_PATH=/up

EXPOSE 8080

USER www-data
