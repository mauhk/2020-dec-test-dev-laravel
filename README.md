# 2020-dec-test-dev-laravel

Requisitos:
- composer
- npm
- docker

Backend:
- Acessar a raiz do projeto e rodar os comandos: 
-- sudo composer install
-- ./vendor/bin/sail up
-- ./vendor/bin/sail artisan migrate
-- ./vendor/bin/sail artisan db:seed

Frontend:
- Acessar a pasta public/site e rodar os comandos:
-- npm install
-- npm run serve

Dados para teste:
-- Login: admin@admin.com / admin