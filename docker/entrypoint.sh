#!/usr/bin/env bash

sudo chown -R arkscan:www-data /var/www/arkscan
touch database/database.sqlite
#--- run installs & build files
composer install --ignore-platform-reqs
yarn install
yarn build
#--- fire up services
sudo supervisord &
sudo chmod -R 775 /var/www/arkscan/database
sudo chmod -R 775 /var/www/arkscan/storage
sudo chmod -R 775 /var/www/arkscan/bootstrap/cache
yarn cache clean
rm -rf ~/.composer/
#--- laravel and cache setup
php artisan key:generate --force
php artisan migrate:fresh --force
php artisan storage:link
#
php artisan explorer:cache-network-aggregates
php artisan explorer:cache-fees
php artisan explorer:cache-transactions
php artisan explorer:cache-prices
php artisan explorer:cache-volume
php artisan explorer:cache-currencies-data
php artisan explorer:cache-validator-aggregates
php artisan explorer:cache-validator-performance
php artisan explorer:cache-validator-productivity
php artisan explorer:cache-validator-resignation-ids
php artisan explorer:cache-validator-wallets
php artisan explorer:cache-validators-with-voters
php artisan explorer:cache-validator-voter-counts
php artisan explorer:cache-blocks
php artisan explorer:cache-transactions
php artisan explorer:cache-address-statistics
php artisan explorer:cache-validator-statistics
php artisan explorer:cache-market-data-statistics
php artisan explorer:cache-annual-statistics --all

#--- run system scheduler
sudo crond
bash
