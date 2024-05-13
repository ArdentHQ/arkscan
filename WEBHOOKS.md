# Webhooks

Before starting, make sure you follow the [Websockets](./WEBSOCKETS.md) steps as webhooks rely on that functionality.

## Setup

There is no additional configuration required for ARK Scan when setting up webhooks.

### ARK Core

Webhooks are disabled by default on ARK Core. They can be enabled by following the steps [here](https://ark.dev/docs/api/webhook-api/getting-started)

### ARK Scan

Once core is setup to handle webhooks, you need to run the following command to setup the new webhook. We generate a Signed URL so that nothing else can push to the webhooks endpoint.

Run `php artisan ark:webhook:setup --host=CORE_IP --port=4004 --event=block.applied`, replacing `CORE_IP` with the IP of your Core node. If you change the port, you will also need to change it here.

You will receive a response with the token for the webhook. Keep this safe as you will need this if you ever decide to remove the webhook from Core.

## Troubleshooting

### HTTPS

Core cannot push webhooks to self-signed certificates.

If you are running valet (or a similar system) to serve websites locally, then you will need to publicly serve your local site:

1. Run `valet serve`
2. Copy the public URL for your local site
3. Paste it into your `.env` for `APP_URL=` (e.g. `APP_URL=https://public-site.com`)
4. Restart the websockets process
