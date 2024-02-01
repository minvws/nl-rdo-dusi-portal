# Portal Backend

## Install

After creating a local git checkout of this repository, getting this project up
and running is a matter of:

Running the init script

### TL;DR

If you already know how all of this works, this should be all the information
you need:

```sh
# 1. Start the applications
./run.sh

# 2. Connect to assessment-portal
By default the portal is accessable at http://localhost:8001
```

## Migrations

To convert the migrations to sql files, run the following command:

```sh
vendor/bin/sail artisan sql-export description_of_the_migrations \
--laravelMigrationsPath=/var/www/html/vendor/minvws/dusi-shared/database/migrations \
--sqlMigrationsPath=/var/www/html/vendor/minvws/dusi-shared/database/sql/dusi_app_db/
```

## Development

This application has been developed in Laravel, please see the [Laravel docs][laravel-docs]
for Laravel specific details.

### Users

### Running the application

To migrate the database, run:

```sh
vendor/bin/sail artisan migrate:fresh --path=vendor/minvws/dusi-application-model/database/migrations
```

### Docker compose

- Before running `docker-compose up`, be sure to create and edit a `.env` file .

- Whenever something changes in the docker setup, don't forget to re-build the
  containers:

  ```sh
  docker-compose up --build --remove-orphans
  ```

- A script that checks the develop environment is run when `docker-compose up`
is run.
- This script will report any problems and try to make suggestions
on how to resolve things.
- These suggestions can usually be run inside the Docker container
 (using `docker-compose exec assessment-web some-command`).

- Docker-compose might show this warning:
  ```WARNING: The [...] variable is not set. Defaulting to a blank string.```
  This is caused when a `.env` file has not been created or when the `.env` file
  is missing a variable used in the docker(-compose) file.

[laravel-docs]: https://laravel.com/docs/9.x

## Testing

### SurePay

> Please note that the sandbox environment is only available from 07:00-20:00 on working days. Outside of these hours
> you’d receive a timeout error. Also, be aware that the IP needs to be whitelisted before you can initiate an API call.
