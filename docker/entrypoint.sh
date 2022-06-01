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
php artisan arkscan:cache-network-aggregates
php artisan arkscan:cache-fees
php artisan arkscan:cache-transactions
php artisan arkscan:cache-prices
php artisan arkscan:cache-currencies-data
php artisan arkscan:cache-currencies-history --no-delay
php artisan arkscan:cache-delegate-aggregates
php artisan arkscan:cache-delegate-performance
php artisan arkscan:cache-delegate-productivity
php artisan arkscan:cache-delegate-resignation-ids
php artisan arkscan:cache-delegate-usernames
php artisan arkscan:cache-delegate-wallets
php artisan arkscan:cache-delegates-with-voters
php artisan arkscan:cache-delegate-voter-counts
php artisan arkscan:cache-multi-signature-addresses

#--- run system scheduler
sudo crond
bash
