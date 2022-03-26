#!/bin/bash
set -e

echo "Deployment started ..."

# Pull repository
git pull origin main

# Install composer dependencies
composer install

# Run database migrations
php artisan migrate --force

# Run passport
php artisan passport:install

# Run storage for images
php artisan storage:link

echo "Deployment finished!"
