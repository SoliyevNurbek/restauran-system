#!/bin/sh
set -eu

php artisan queue:work --sleep=3 --tries=3 --max-time=3600 --no-interaction
