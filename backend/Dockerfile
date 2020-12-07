FROM php:7.4-fpm-alpine


RUN apk add --no-cache \
    $PHPIZE_DEPS \
    openssl-dev

# Set working directory
WORKDIR /var/www

RUN docker-php-ext-install pdo pdo_mysql

RUN pecl install -o -f redis \
&&  rm -rf /tmp/pear \
&&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN chown -R www-data:www-data /var/www


