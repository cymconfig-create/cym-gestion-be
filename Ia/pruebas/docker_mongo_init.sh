#!/usr/bin/env bash
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive
apt-get update -qq
apt-get install -y -qq \
  libssl-dev pkg-config zlib1g-dev libcurl4-openssl-dev \
  autoconf dpkg-dev file g++ gcc libc-dev make re2c

printf '\n' | pecl install -q mongodb-1.21.3
echo 'extension=mongodb.so' > /usr/local/etc/php/conf.d/docker-php-ext-mongodb.ini

php -r "if (!extension_loaded('mongodb')) { fwrite(STDERR, 'mongodb ext missing\n'); exit(1); }"
php -m | grep -i mongo || true
cd /app
php artisan ia:mongo-init-schemas
