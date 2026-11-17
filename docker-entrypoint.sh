#!/bin/bash
set -e

php artisan storage:link --force || true
php artisan migrate --force || true
php artisan optimize

exec apache2-foreground
