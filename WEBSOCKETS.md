# Websockets

In order to setup webhooks for ARK Scan, there are several steps you need to go through. We will assume the local domain used is `arkscan.test`, but adjust it where necessary.

## General Requirements

### Installation

1. Run `composer install`
2. Run `pnpm install`
3. Run `php artisan reverb:install`

## Config

The final installation step will setup reverb configuration. The main configuration being `REVERB_APP_ID`, `REVERB_APP_KEY`, and `REVERB_APP_SECRET`. Below you'll find different configurations depending on whether you're using HTTP or HTTPS.

### Generic .env values

The below config values are the same regardless of which scheme you are using:

```bash
BROADCAST_DRIVER=reverb

REVERB_SERVER_HOST=0.0.0.0
REVERB_SERVER_PORT=8080

REVERB_APP_ID=123456
REVERB_APP_KEY=12345678901234567890
REVERB_APP_SECRET=12345678901234567890
REVERB_HOST="arkscan.test"

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"

VITE_REVERB_PORT_TLS="${REVERB_PORT_TLS}"
```

### HTTP

```bash
APP_URL=http://arkscan.test

REVERB_SCHEME=http
REVERB_PORT=8080
```

### HTTPS

Run `valet secure`.

```bash
APP_URL=https://arkscan.test

REVERB_SCHEME=https
REVERB_PORT=8080
REVERB_PORT_TLS=8080
```

If running locally with a self-signed certificate, you will need to add the below option:

```bash
REVERB_VERIFY_PEERS=false
```

## Running

1. Start the websocket server with `php artisan reverb:start`. You may specify different arguments to the `.env` file with `php artisan reverb:start --host="0.0.0.0" --port=8080 --hostname="arkscan.test"`
2. In a separate window, run `pnpm dev`
3. Load ARK Scan and go to `arkscan.test/blocks`
4. In a separate window, run `php artisan horizon`
5. In a separate window, run `php artisan tinker`

In devtools, the websocket should connected and there should be no errors.

In the tinker window, run `NewBlock::dispatch()` which should trigger a reload of the Blocks table.

## Webhooks

Once this is all setup and running correctly, you will need to then setup webhooks, which can be found [here](./WEBHOOKS.md).

## Setup on Forge

> A short overview on what to do when setting up websockets on Forge with Laravel Reverb

-   Ensure that you run PHP 8.2+
-   Click the `Reverb` toggle on the site's page in Forge to enable the Reverb setup. Use port `8080` and the URL on which you want the websockets to run (e.g. a `ws` subdomain)
-   Go to the SSL tab in Forge and issue a new certificate for the domain + the subdomain on which the websockets run
-   Ensure `BROADCAST_DRIVER=reverb` is set in your `.env`, and possibly set `REVERB_PORT=443` and `REVERB_SCHEME="https"` if not done automatically yet.

## Troubleshooting

### Valet HTTPS Failed Connection

If there are issues connecting to the websocket when using HTTPS, you may need to connect to websockets through nginx. Use the below config to setup an nginx proxy:

#### Nginx Configuration

Open the valet nginx config (e.g. `~/.valet/Nginx/arkscan.test` or `~/.config/valet/Nginx/arkscan.test`). Under the ssl entry, you'll need to add the following configuration. Adjust the port if necessary:

```nginx
server {
    listen 443;

    ...

    location /app {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        proxy_pass http://0.0.0.0:8080;
    }

    ...
}
```

Then update your .env to reflect this:

```bash
REVERB_PORT_TLS=443
```

### Swoole in PHP 8.2+

If you run into an issue where you cannot connect to a PostgreSQL database after updating to PHP 8.2, [it may have to do with the swoole extension in PHP 8.2+](https://github.com/php/php-src/issues/14665). The error will be something along the lines of

```
SQLSTATE[08006] [7] could not send SSL negotiation packet: Resource temporarily unavailable (Connection: pgsql, SQL: (select * from ........)
```

You can get around this by removing (or renaming) the configuration file, e.g. `sudo mv /etc/php/8.2/mods-available/swoole.ini /etc/php/8.2/mods-available/swoole-backup.ini`
