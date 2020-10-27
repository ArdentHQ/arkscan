# Template - Laravel

<p align="center">
    <img src="./banner.png" />
</p>

[![Build Status](https://badgen.now.sh/github/status/ArkEcosystem/explorer/develop)](https://github.com/ArkEcosystem/explorer/actions?query=branch%3Adevelop)
[![Codecov](https://badgen.now.sh/codecov/c/github/arkecosystem/explorer)](https://codecov.io/gh/arkecosystem/explorer)
[![License: MIT](https://badgen.now.sh/badge/license/MIT/green)](https://opensource.org/licenses/MIT)

> Lead Maintainer: [Michel Kraaijeveld](https://github.com/ItsANameToo)

## Installation

### Requirements

-   [Composer](https://getcomposer.org)
-   [Valet](https://laravel.com/docs/8.x/valet) or [Homestead](https://laravel.com/docs/8.x/homestead)

### Development

Currently the instructions are for Valet

```bash
git clone https://github.com/ArkEcosystem/explorer.ark.io.git
cd explorer.ark.io
composer install
yarn install

cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
## You can run these commands to create a core database with fake data (change EXPLORER_DB_DATABASE to your actual database name))
# php artisan migrate --path=tests/migrations --database=explorer
# composer play
php artisan storage:link
yarn run watch

valet link explorer-ark-io
```

#### Caching

The explorer relies on a lot of data to be readily available without having to hit the database for every visitor that opens the website. This is achieved by storing commonly used data like votes, last forged blocks and delegates within redis. During development you should run the following commands manually. These commands will run automatically via cronjobs in a production environment.

##### Cache Pricing and Fee data

```
php artisan cache:charts
```

##### Cache total amounts, fees and rewards forged

```
php artisan cache:delegate-aggregates
```

##### Cache active delegates for the current round

```
php artisan cache:delegates
```

##### Cache the last blocks forged by the delegates of the current round

```
php artisan cache:last-blocks
```

##### Cache wallets that have received a vote

```
php artisan cache:votes
```

##### Cache the past performance of delegates in the current round

```
php artisan cache:past-round-performance
```

*Important:* You will need access to a Core Postgres database, or use the commented lines above to fill it with dummy data. The details can be specified in the `.env` file under `EXPLORER_DB_*`.

Afterwards, you can navigate to `explorer-ark-io.test` in your browser to see it in action

### Production

```bash
git clone https://github.com/ArkEcosystem/explorer.ark.io.git
cd explorer.ark.io
composer install
yarn install

cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
php artisan storage:link
```

*Important:* You will need access to a Core Postgres database. The details can be specified in the `.env` file under `EXPLORER_DB_*`.

## Security

If you discover a security vulnerability within this package, please send an e-mail to security@ark.io. All security vulnerabilities will be promptly addressed.

## Credits

This project exists thanks to all the people who [contribute](../../contributors).

## License

[MIT](LICENSE) © [ARK Ecosystem](https://ark.io)
