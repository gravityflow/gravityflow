FROM php:7.4-cli

MAINTAINER Steve Henty steve@gravityflow.io


# Install required system packages
RUN apt-get update && \
    apt-get -y install \
            git \
            rsync \
            libssl-dev \
            libfreetype6-dev \
            libjpeg62-turbo-dev \
            sudo less \
            zlib1g-dev \
            libssl-dev \
            libzip-dev \
            mariadb-client \
            libpcre3 \
            libpcre3-dev \
            zip unzip \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install php extensions
RUN docker-php-ext-install \
    bcmath \
    gd \
    zip

RUN docker-php-ext-install -j$(nproc) iconv \
        && docker-php-ext-configure gd --with-freetype --with-jpeg \
        && docker-php-ext-install -j$(nproc) gd


# Add mysql driver required for wp-browser
RUN docker-php-ext-install mysqli

# Configure php
RUN echo "date.timezone = UTC" >> /usr/local/etc/php/php.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin


# Add WP-CLI
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
RUN chmod +x wp-cli.phar
RUN mv wp-cli.phar /usr/local/bin/wp

# Prepare application
WORKDIR /repo

# Install vendor
COPY ./composer.json /repo/composer.json
RUN composer install --prefer-dist --optimize-autoloader

# Add source-code
COPY . /repo

WORKDIR /project

ADD docker-entrypoint.sh /

RUN ["chmod", "+x", "/docker-entrypoint.sh"]
