#!/bin/bash
set -e

echo "Deployment started ..."

#Test if we are not mongole
ssh -vT git@github.com

# Pull the latest version of the app
git pull origin main

# Install composer dependencies
composer install

# Run database migrations
php artisan migrate

# Run passport
php artisan passport:install

# Run storage for images
php artisan storage:link

echo "Deployment finished!"
