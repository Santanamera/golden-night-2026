FROM php:8.2-cli

WORKDIR /var/www/html

RUN apt-get update \
    && apt-get install -y --no-install-recommends libicu-dev libonig-dev libzip-dev unzip libfreetype6-dev libjpeg-dev libpng-dev libwebp-dev zlib1g-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install pdo pdo_mysql mysqli fileinfo gd mbstring \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html

EXPOSE 8080

CMD ["sh", "-c", "cd /var/www/html && php -S 0.0.0.0:${PORT:-8080} -t ."]
