# Production

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
