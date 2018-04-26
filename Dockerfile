FROM php

RUN apt-get update && apt-get install -y zlib1g-dev \
    && docker-php-ext-install zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install PHP XDebug
RUN pecl install xdebug && docker-php-ext-enable xdebug

WORKDIR /opt/project
