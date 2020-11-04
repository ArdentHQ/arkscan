# Caching

The explorer relies on a lot of data to be readily available without having to hit the database for every visitor that opens the website. This is achieved by storing commonly used data like votes, last forged blocks and delegates within redis. During development you should run the following commands manually. These commands will run automatically via cronjobs in a production environment.

## Cache total amounts, fees and rewards forged

```
php artisan cache:delegate-aggregates
```

## Cache active delegates for the current round

```
php artisan cache:delegates
```

## Cache exchange and price data

```
php artisan cache:exchange-rates
```

## Cache fee chart data

```
php artisan cache:chart-fee
```

## Cache the last blocks forged by the delegates of the current round

```
php artisan cache:last-blocks
```

## Cache wallet addresses that have a multi-signature

```
php artisan cache:musig
```

## Cache network statistics

```
php artisan cache:statistics
```

## Cache the past performance of all delegates in the current round

```
php artisan cache:past-round-performance
```

## Cache the productivity of all delegates in the current round

```
php artisan cache:productivity
```

## Cache real-time statistics like height and supply

```
php artisan cache:real-time-statistics
```

## Cache transaction IDs for all resigned delegates

```
php artisan cache:resignation-ids
```

## Cache wallets that have received a vote

```
php artisan cache:usernames
```

## Cache the voter count for each delegate

```
php artisan cache:voter-count
```

## Cache wallets that have been voted for to avoid duplicate queries

```
php artisan cache:votes
```
