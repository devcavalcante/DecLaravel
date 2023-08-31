FROM php:8.1-fpm-alpine

ARG APP_ENV
ENV APP_ENV $APP_ENV
ARG XDEBUG_MODE
ENV XDEBUG_MODE $XDEBUG_MODE
ARG XDEBUG_IDEKEY
ENV XDEBUG_IDEKEY $XDEBUG_IDEKEY
ARG XDEBUG_HANDLER
ENV XDEBUG_HANDLER $XDEBUG_HANDLER
ARG XDEBUG_PORT
ENV XDEBUG_PORT $XDEBUG_PORT

RUN apk add --y --no-cache openssl bash nodejs npm postgresql-dev
RUN docker-php-ext-install bcmath pdo pdo_pgsql opcache

WORKDIR /app

RUN rm -rf /app
RUN ln -s public html

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /app

RUN chown -R www:www /app && chmod -R 755 /app

RUN cp -R ./docker/gitHooks/ ./.git/hooks/
RUN composer update --optimize-autoloader
RUN php artisan key:generate && php artisan config:cache

EXPOSE 9000
ENTRYPOINT [ "php-fpm" ]

