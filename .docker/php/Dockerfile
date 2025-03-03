FROM php:8.1-fpm

RUN apt-get update \
    && apt-get install -y \
        libicu-dev libzip-dev \
        curl \
        libcurl4-openssl-dev \
        zip \
        libwebp-dev \
        libjpeg62-turbo-dev \
        libpng-dev libxpm-dev \
        libfreetype6-dev \
        vim \
        librabbitmq-dev \
        ffmpeg

RUN curl -fsSL https://deb.nodesource.com/setup_21.x | bash - &&\
    apt-get install -y nodejs

RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install gd

RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN docker-php-ext-install intl curl zip pdo_mysql opcache

RUN pecl install redis \
        && docker-php-ext-enable redis

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY .docker/php/php.ini /usr/local/etc/php/conf.d/

WORKDIR /app

CMD ["php-fpm"]
