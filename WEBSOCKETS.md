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

        proxy_pass http://0.0.0.0:8082;
    }

    ...
}
```

Then update your .env to reflect this:

```bash
REVERB_PORT_TLS=443
```
