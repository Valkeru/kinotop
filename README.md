Запуск проекта:
```bash
composer install
docker-compose up -d
php bin/console server:start
```

Для обновления БД:  
```bash
php bin/console kinopoisk:get-data
php bin/console kinopoisk:get-data --date 2018-10-12
```
