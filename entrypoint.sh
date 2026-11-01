#!/bin/bash

# Create storage symlink
php artisan storage:link --force

# Run database migrations
php artisan migrate --force

# Start Apache in foreground
apache2-foreground