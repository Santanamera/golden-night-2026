FROM php:8.2-fpm-alpine

WORKDIR /app

RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    zlib-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli fileinfo gd mbstring

COPY . /app

COPY --chown=www-data:www-data . /app

RUN mkdir -p /app/assets/uploads/{tickets,candidates,demo} /app/database && \
    chown -R www-data:www-data /app

COPY docker-nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 8080

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
