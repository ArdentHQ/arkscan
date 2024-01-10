#!/usr/bin/env bash

sudo chown -R arkscan:www-data /var/www/arkscan
touch database/database.sqlite
#--- run installs
composer install --ignore-platform-reqs
yarn install
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
php artisan explorer:cache-currencies-history --no-delay
php artisan explorer:cache-delegate-aggregates
php artisan explorer:cache-delegate-performance
php artisan explorer:cache-delegate-productivity
php artisan explorer:cache-delegate-resignation-ids
php artisan explorer:cache-delegate-usernames
php artisan explorer:cache-delegate-wallets
php artisan explorer:cache-delegates-with-voters
php artisan explorer:cache-delegate-voter-counts
php artisan explorer:cache-multi-signature-addresses
php artisan explorer:cache-blocks
php artisan explorer:cache-transactions
php artisan explorer:cache-address-statistics
php artisan explorer:cache-delegate-statistics
php artisan explorer:cache-market-data-statistics
php artisan explorer:cache-annual-statistics --all

#--- run system scheduler
sudo crond
bash
