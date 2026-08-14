FROM php:8.2-fpm-alpine

# Build timestamp to force cache bust - unique per build
RUN echo "Build timestamp: $(date '+%s')" > /build.info && cat /build.info

# Build timestamp to force cache bust and verify fresh deploy
ARG BUILD_DATE
RUN echo "Build Date: ${BUILD_DATE}" && date

# Force cache bust - rebuild timestamp  
ENV REBUILD_DATE="2026-08-14-v5-nodeps"

WORKDIR /app

RUN apk add --no-cache \
    nginx \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    zlib-dev \
    oniguruma-dev \
    libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql mysqli fileinfo gd mbstring zip

# Clean build - remove any old files first
RUN rm -rf /app/* /app/.* 2>/dev/null || true

COPY . /app

COPY --chown=www-data:www-data . /app

COPY php.ini /usr/local/etc/php/conf.d/app.ini

RUN mkdir -p /app/assets/uploads/{tickets,candidates,demo} /app/database && \
    chown -R www-data:www-data /app && \
    grep -q "poem" /app/index.html || (echo "ERROR: Poetic content not found in index.html!" && exit 1)

COPY docker-nginx.conf /etc/nginx/http.d/default.conf

EXPOSE 8080

# Create startup wrapper that ensures fresh files
RUN echo '#!/bin/sh' > /startup.sh && \
    echo 'echo "Ensuring fresh files from image..."' >> /startup.sh && \
    echo 'mkdir -p /app && cd /app' >> /startup.sh && \
    echo 'php-fpm -D && nginx -g "daemon off;"' >> /startup.sh && \
    chmod +x /startup.sh

CMD ["/startup.sh"]
