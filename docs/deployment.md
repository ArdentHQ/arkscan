# Deployment

Deploying the Explorer requires a few things to guarantee smooth operation. If you don't want to deal with all of the deployment hassle we would recommend to use [Laravel Forge](https://forge.laravel.com/) and [Laravel Envoyer](https://envoyer.io/) or [Laravel Vapor](https://vapor.laravel.com/) if you prefer to go serverless and forget about scaling.

## Installation

### Clone

```bash
git clone https://github.com/ArkEcosystem/explorer.ark.io.git explorer
cd explorer
composer install
yarn install
```

### Preparing Application

```bash
cp .env.prod .env
php artisan key:generate
php artisan migrate:fresh
php artisan storage:link
```

### Configuring Environment

```bash
APP_NAME=Laravel
APP_URL=http://localhost

EXPLORER_DB_HOST=127.0.0.1
EXPLORER_DB_PORT=5432
EXPLORER_DB_DATABASE=homestead
EXPLORER_DB_USERNAME=homestead
EXPLORER_DB_PASSWORD=password
```

*Important:* You will need access to a Core database. The details can be specified in the `.env` file under `EXPLORER_DB_*`.

### Cronjobs

Explorer performs a lot of tasks in the background. These tasks are executed on a specific schedule and require the task scheduler to be set up. Take a look at the official [Starting The Scheduler](https://laravel.com/docs/8.x/scheduling#starting-the-scheduler) guide by [Laravel](https://laravel.com/).

> If you are using Laravel Forge you can create this through their "Scheduler" UI.

### Daemons

Explorer performs a lot of tasks in the background. These tasks are executed on a specific schedule and require the task scheduler to be set up. Take a look at the official [Starting The Scheduler](https://laravel.com/docs/8.x/scheduling#starting-the-scheduler) guide by [Laravel](https://laravel.com/).

#### Starting Horizon

Take a look at the official [Deploying Horizon](https://laravel.com/docs/8.x/horizon#deploying-horizon) guide by [Laravel](https://laravel.com/).

> If you are using Laravel Forge you can create this through their "Daemons" UI.

#### Starting Short Schedule

Take a look at the official [Deploying Short Schedule](https://github.com/spatie/laravel-short-schedule#installation) guide by [Laravel](https://spatie.be/).

> If you are using Laravel Forge you can create this through their "Daemons" UI.

### Caching

Now that the task scheduler and Horizon are running you'll need to run the below commands in order to cache all of the data that is required for the Explorer to function.

```bash
php artisan cache:real-time-statistics
php artisan cache:statistics
php artisan cache:last-blocks
php artisan cache:usernames
php artisan cache:musig
php artisan cache:delegate-aggregates
php artisan cache:delegates
php artisan cache:exchange-rates
php artisan cache:chart-fee
php artisan cache:past-round-performance
php artisan cache:productivity
php artisan cache:votes
php artisan cache:resignation-ids
```

## Updates

When updating the Explorer there are a few things to keep in mind. All of them should be executed in the specified order to avoid unexpected issues. You shouldn't

### Restart Horizon

```bash
php artisan horizon:purge
php artisan horizon:terminate
```

### Clear Cache

```bash
php artisan responsecache:clear
php artisan view:clear
```

### Cache Configuration

```bash
php artisan config:cache
```

### Cache Routes

```bash
php artisan route:cache
```
