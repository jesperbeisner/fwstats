FROM php:8.4-cli

ARG USER_ID
ARG USER_NAME

WORKDIR "/app"

RUN apt-get update && \
    apt-get -y upgrade && \
    apt-get -y install --no-install-recommends nano htop && \
    curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions pcntl swoole @composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

RUN useradd --create-home --shell /bin/bash --user-group $USER_NAME --uid $USER_ID

USER $USER_NAME

ENTRYPOINT [ "php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080" ]
