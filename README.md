# Snowtricks v1

Community Website made with Symfony framework for sharing snowboard tricks.

[![Maintainability](https://api.codeclimate.com/v1/badges/3ff9fec3772a81e00946/maintainability)](https://codeclimate.com/github/sjaulin/snowtricks/maintainability)

## Installation

1. Configure file .env.local using file .env.local.example as a template.

2. Install dependancies :

```
composer install
```

3. Create the database if not exist

```
php bin/console doctrine:database:create
```

4. Create database tables

```
php bin/console doctrine:migrations:migrate
```

5. If necessary, create fake content and demo users

```
php bin/console doctrine:fixtures:load --env=dev --group=app
```

6. Once ready for production, modify the environment in .env file

```
# ./.env
APP_ENV=prod
```
