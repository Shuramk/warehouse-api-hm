Installation
------------
```shell
git clone
docker-compose up -d --build
```

```shell
docker-compose exec php composer install
docker-compose exec php php bin/console doctrine:migrations:migrate -n
docker-compose exec php php bin/console doctrine:fixtures:load -n
```

`Swagger:`
http://localhost:8000/api/doc

`graphiql:`
http://127.0.0.1:8000/api/doc/graphiql