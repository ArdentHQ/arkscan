# Webhooks

Before starting, make sure you follow the [Websockets](./WEBSOCKETS.md) steps as webhooks rely on that functionality.

## ARK Core

Webhooks are disabled by default on ARK Core. They can be enabled by following the steps [here](https://ark.dev/docs/api/webhook-api/getting-started)

## ARK Scan

The following areas of ARK Scan use webhook events to reload their data:

- Blocks Page
- Transactions Page
- Wallet Page - Transactions Tab
- Wallet Page - Validated Blocks Tab (Delegates)
- Wallet Page - Voters Tab (Delegates)

### Management

#### Setup

Once core is setup to handle webhooks, you need to run the following command to setup the new webhook. We generate a Signed URL so that nothing else can push to the webhooks endpoint.

Run `php artisan ark:webhook:setup --host=CORE_IP --port=CORE_PORT --event=block.applied`, replacing `CORE_IP` with the IP of your Core node. If you change the port, you will also need to change it here.

You will receive a response with the token for the webhook. Keep this safe as you will need this if you ever decide to remove the webhook from Core.

#### List

To list all webhooks which have been previously setup, you can run `php artisan ark:webhook:list`. This will detail the Event as well as the ARK Core details associated with the webhook.

An example output is as follows:

```Text
+--------------------------------------+----------------+---------------------+------------------------------------------------------------------+
| ID                                   | Host           | Event               | Token                                                            |
+--------------------------------------+----------------+---------------------+------------------------------------------------------------------+
| c19b5288-ef67-4eea-ae5e-a0e3343a8084 | 127.0.0.1:4004 | block.applied       | 4eeb65d48b0cebf33d4dd50733dc67a6958e6fc291e32755d922e78f82da75d0 |
| d1097193-4078-420a-bb6c-25b584be80c4 | 127.0.0.1:4004 | wallet.vote         | 3d676d8af8737293666f6f3d73684f8e8cbe2400c68d996802b7bd25f3cd7310 |
| d4c6ee09-9599-4c85-82ef-cfc4d6072015 | 127.0.0.1:4004 | transaction.applied | 4506be5c25ccd1f7bcd8ab0ef877723e35137be3912f6a8c00dc2aa192c765b8 |
+--------------------------------------+----------------+---------------------+------------------------------------------------------------------+
```

#### Delete

To delete a webhook, you first need to know the ID or token of it. You can do the above command to find them both.

To delete with an ID, run:

`php artisan ark:webhook:delete --id=WEBHOOK_ID`

To delete with a token, run:

`php artisan ark:webhook:delete --token=WEBHOOK_TOKEN`

By default, the ARK Core details which are stored against the webhook are used to perform the deletion request. You can override these with the `--host` and `--port` arguments.

#### Flush

You can also remove all webhooks in one go. All you need to do is run:

`php artisan ark:webhook:flush`

As with the delete command, by default, the ARK Core details which are stored against the webhook are used to perform the deletion request. You can override these with the `--host` and `--port` arguments.

### Events

The below are the available events and how to set them up:

| Event               | Description                       | Channels                                                                       |
| ------------------- | --------------------------------- | ------------------------------------------------------------------------------ |
| block.applied       | Triggers a `NewBlock`       event | `blocks`, `blocks.<generatorPublicKey>`                                        |
| transaction.applied | Triggers a `NewTransaction` event | `transactions`, `transactions.<senderPublicKey>`, `transactions.<recipientId>` |
| wallet.vote         | Triggers a `WalletVote` event     | `wallet-vote.<votePublicKey>`, `wallet-vote.<unvotePublicKey>`                 |

They can be setup using the setup command (replacing `CORE_IP`, `CORE_PORT` and `EVENT`):

`php artisan ark:webhook:setup --host=CORE_IP --port=CORE_PORT --event=EVENT`

## Troubleshooting

### HTTPS

Core cannot push webhooks to self-signed certificates.

If you are running valet (or a similar system) to serve websites locally, then you will need to publicly serve your local site:

1. Run `valet serve`
2. Copy the public URL for your local site
3. Paste it into your `.env` for `APP_URL=` (e.g. `APP_URL=https://public-site.com`)
4. Restart the websockets process
