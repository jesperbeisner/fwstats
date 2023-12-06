FROM php:8.3-apache

ARG USER_ID
ARG USER_NAME

WORKDIR "/var/www/html"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions pdo_mysql xdebug @composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY ".docker/dev/apache2.conf" "/etc/apache2/apache2.conf"
COPY ".docker/dev/php-extra.ini" "$PHP_INI_DIR/conf.d/php-extra.ini"

RUN a2enmod headers

RUN useradd --create-home --shell /bin/bash -U $USER_NAME -u $USER_ID

USER $USER_NAME
