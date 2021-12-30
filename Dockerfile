# Minimal Dockerfile for a stand alone PHP script i.e. with no dependencies
# to any other services like MySQL or PostreSQL f.i.
# 1. Copy this file in your application root
# 2. Run `docker build --pull --rm -t cavo789/runcode:1.0 .`
# 3. Run `docker run -p 80:80 -d --name runcode cavo789/runcode:1.0`
#    OR, during the development:
#    Run `docker run -p 80:80 -d -v ${PWD}:/var/www/html --name runcode cavo789/runcode:1.0`
#    This last command will allow you to edit the index.php script and have the changes
#    reflected in the Docker container directly
#
# (Publish on Docker hub: `docker push cavo789/runcode:1.0`)

# Desired target PHP version, See https://hub.docker.com/_/php for more
ARG PHP_VERSION=7.4

FROM php:${PHP_VERSION}-apache

# Install composer; copy folder /usr/bin/composer of the image to our /usr/local/bin/composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN chmod +x /usr/local/bin/composer;

# Install ZIP and UNZIP required by composer to install libraries
RUN set -e -x; \
    apt-get update -yqq; \
    apt-get install -y --no-install-recommends zip unzip; \
    apt-get install -y libicu-dev && docker-php-ext-configure intl && docker-php-ext-install intl; \
    # apt-get install -y --no-install-recommends php7.4-intl; \
    # cleanup the apt-get install cache and any tmp folder
    apt-get clean; \
    rm -rf /tmp/*; \
    rm -rf /var/list/apt/*; \
    rm -rf /var/lib/apt/lists/*

# PHP-CS-FIXER
RUN set -e -x; \
    curl -sSL https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v3.3.0/php-cs-fixer.phar -o /usr/local/bin/php-cs-fixer.phar; \
    chmod +x /usr/local/bin/php-cs-fixer.phar

# PHPCBF (Code beautifer)
RUN set -e -x; \
    curl -sSL https://github.com/squizlabs/PHP_CodeSniffer/releases/download/3.6.0/phpcs.phar -o /usr/local/bin/phpcs.phar; \
    curl -sSL https://github.com/squizlabs/PHP_CodeSniffer/releases/download/3.6.0/phpcbf.phar -o /usr/local/bin/phpcbf.phar; \
    chmod +x /usr/local/bin/phpcs.phar /usr/local/bin/phpcbf.phar

RUN set -e -x; \
    chmod 0777 /tmp/ ; \
    touch /tmp/runPhp_code.php ; \
    chmod 0777 /tmp/runPhp_code.php

WORKDIR /var/www/html

COPY . .
EXPOSE 80
