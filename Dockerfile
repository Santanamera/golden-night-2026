FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libonig-dev libzip-dev unzip \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

EXPOSE 8080

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-8080} -t /var/www/html"]
