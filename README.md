# currency_exchange

## Описание

Проект представляет собой сервис для конвертации валют.

## INSTALL

1. clone project

```
git clone project
```

2. docker up:

```
cd /path/to/project/.docker
docker-compose up -d 
```

3. composer install:

```
docker exec -it currency_exchange_php composer install
```

4. set up .env:

- Необходимо заполнить следующие поля в файле .env:

```
EXCHANGERATE_API_KEY - ключ для доступа к API сервиса exchangerate-api
DATABASE_URL - строка подключения к базе данных
```

5. init db:

```
docker exec -it currency_exchange_php bin/console doctrine:migrations:migrate -n
```

6. first fill db:

```
docker exec -it currency_exchange_php bin/console app:initial_currency_data
```

## Additional commands

* Для регулярного обновления курсов валют необходимо добавить в cron следующую команду:

```
docker exec -it currency_exchange_php bin/console app:update_or_add_currency
```

## API

* GET /currency-convert
* GET /api/currency-convert?fromCurrency=USD&toCurrency=ANG&amount=100

