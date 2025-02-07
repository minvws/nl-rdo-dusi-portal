FROM php:8.2-cli-alpine

RUN apk update && \
    apk add --no-cache $PHPIZE_DEPS && \
    docker-php-ext-configure pcntl --enable-pcntl && \
    docker-php-ext-install pcntl && \
    docker-php-ext-enable pcntl