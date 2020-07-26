FROM php:7.4

RUN apt-get update && apt-get install -yq \
    git \
    unzip \
    curl \
    gnupg \
    libzip-dev \
    zlib1g-dev libicu-dev \
    libsqlite3-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev

# PHP Extensions
RUN docker-php-ext-install pdo_mysql zip intl

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

# Node & Yarn
RUN curl -sL https://deb.nodesource.com/setup_12.x | bash && \
    apt-get install -y nodejs
