# Build stage: install PHP dependencies
FROM php:8.3-cli AS build

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --no-autoloader

COPY . .

RUN composer dump-autoload --no-dev --optimize


# Runtime stage: only PHP and the application
FROM php:8.3-cli

WORKDIR /app

COPY --from=build --chown=www-data:www-data /app /app

ENV PORT=10000

EXPOSE 10000

USER www-data

CMD ["php", "Aifolou/server.php"]