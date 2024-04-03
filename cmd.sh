#!/bin/sh

sleep 30
php artisan migrate
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000