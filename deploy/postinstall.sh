#!/bin/bash


cd /var/www/html

composer install --no-interaction --optimize-autoloader

php bin/console cache:clear
php bin/console cache:warmup

# Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# Set correct permissions
chown -R www-data:www-data var
chmod -R 775 var

echo "Post-installation completed successfully!"
