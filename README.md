# Iniciando o container

Rode os seguintes comandos em sequência:

1. `cp .env.example .env`
2. `docker compose build`
3. `docker compose run app php artisan composer install`
4. `docker compose run app php artisan migrate`
5. `docker compose run app php artisan key:generate`
6. `docker compose run app php artisan jwt:secret`
