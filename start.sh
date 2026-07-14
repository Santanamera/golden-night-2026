#!/bin/sh
set -eu

ROOT_DIR="${APP_ROOT:-/app}"
if [ ! -d "$ROOT_DIR" ]; then
  ROOT_DIR="$(pwd)"
fi

mkdir -p /tmp

php-fpm -y "$ROOT_DIR/php-fpm.conf" -g /tmp/php-fpm.pid
exec nginx -c "$ROOT_DIR/nginx.conf" -g 'daemon off;'
