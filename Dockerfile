FROM php:7.0-cli

MAINTAINER Steve Henty steve@gravityflow.io

# Install required system packages
RUN apt-get update && \
    apt-get -y install \
            git \
            zlib1g-dev \
            libssl-dev \
            mysql-client \
            sudo less \
        --no-install-recommends && \
        apt-get clean && \
        rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Install php extensions
RUN docker-php-ext-install \
    bcmath \
    zip

# Add mysql driver required for wp-browser
RUN docker-php-ext-install mysqli

# Configure php
RUN echo "date.timezone = UTC" >> /usr/local/etc/php/php.ini

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin
RUN composer global require --optimize-autoloader \
        "hirak/prestissimo"


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
