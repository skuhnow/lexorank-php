FROM php:7.2.24-fpm-alpine

ENV COMPOSER_ALLOW_SUPERUSER 1

# Install PHP-Dependencies
RUN apk update && apk upgrade && apk add bash git

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install xdebug-2.9.6 \
    && docker-php-ext-enable xdebug \
    && apk del -f .build-deps

# Install Symfony-Command & Composer
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php composer-setup.php --filename=composer \
    && php -r "unlink('composer-setup.php');" \
    && mv composer /usr/local/bin/composer

# Set working directory
WORKDIR /srv/app/

# Copy source (will be overwritten by bind mount)
COPY composer.json composer.lock ./
COPY src src/
COPY tests tests/
COPY phpunit.xml ./

RUN mkdir -p var/cache var/logs \
    && composer install --prefer-dist --optimize-autoloader --classmap-authoritative --no-scripts --no-progress --no-suggest \
    && composer dump-autoload --optimize  --classmap-authoritative \
    && composer clear-cache

CMD [ "vendor/bin/phpunit" ]
