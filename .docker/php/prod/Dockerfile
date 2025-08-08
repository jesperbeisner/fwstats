FROM php:8.4-cli AS assets

WORKDIR "/app"

RUN apt-get update && \
    apt-get -y upgrade && \
    curl -fsSL https://deb.nodesource.com/setup_24.x | bash - && \
    apt-get install -y nodejs && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/*

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions @composer

RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY . .

RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs
RUN npm install-clean && npm run build

FROM php:8.4-cli

LABEL org.opencontainers.image.source=https://github.com/jesperbeisner/fwstats

WORKDIR "/app"

ADD https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/
RUN chmod +x /usr/local/bin/install-php-extensions && install-php-extensions pcntl swoole

RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY . .

RUN useradd --create-home --shell /bin/bash --uid 1000 --user-group fwstats

RUN chown -R fwstats:fwstats .

COPY --chown=fwstats:fwstats --from=assets /app/public public
COPY --chown=fwstats:fwstats --from=assets /app/vendor vendor

USER fwstats

ENTRYPOINT ["php", "artisan", "octane:start", "--host=0.0.0.0", "--port=8080"]
