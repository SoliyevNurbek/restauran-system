#!/bin/sh
set -eu

php artisan optimize:clear

if [ ! -L public/storage ]; then
  php artisan storage:link
fi

php artisan migrate --force
php artisan optimize
