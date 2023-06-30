# Portal Web

## Install

After creating a local git checkout of this repository, getting this project up
and running is a matter of:

Running the init script

### TL;DR:

Make sure you have PHP 8.2 and composer installed.

On Mac:
- First install [Homebrew](https://brew.sh).
- Run `brew install php@8.2`
- Run `brew install composer`

On Linux and Windows Subsystem for Linux use your package manager.

This service depends on services from the `nl-rdo-dusi-portal-backend` and `nl-rdo-dusi-form-web` repositories. Make
sure to install these first.

After cloning `nl-rdo-dusi-form-web` inside the `nl-rdo-dusi-form-web` directory:
```sh
# 1. Copy the .env.example to .env and start the application
./init.sh

# 2. Migrate the database and seed the form(s)
vendor/bin/sail artisan migrate --seed
```

After cloning `nl-rdo-dusi-portal-backend` inside the `nl-rdo-dusi-portal-backend` directory:
```sh
# 1. Copy the .env.example to .env and start the application
./init.sh

# 2. Migrate the database
vendor/bin/sail artisan migrate
```
And finally for `nl-rdo-dusi-portal-web` inside the `nl-rdo-dusi-portal-web` directory:
```sh
# 1. Copy the .env.example to .env and start the application
./init.sh

# 2. Connect to portal
By default the portal is accessible at http://localhost:8000
```

At this time the Vue application frontend is separate from the web API. To start the Vue application:
```sh
cd vue
npm i
cp .env.example .env
npm run dev
```

NOTE: at this time the Vue application is only available in the `origin/feature/vue-jsonforms-storybook` branch.

## Usage


## Development

This application has been developed in Laravel, please see the [Laravel docs][laravel-docs]
for Laravel specific details.

### Laravel Sail / Docker compose

Docker compose is wrapped using Laravel Sail. Instead of using `docker compose` please use `vendor/bin/sail` to make
sure the commands are run with the correct environment setup.

- Whenever something changes in the docker setup, don't forget to re-build the
  containers:
  ```sh
  vendor/bin/sail up --build --remove-orphans
  ```

- Sail / docker compose might show this warning:
  ```WARNING: The [...] variable is not set. Defaulting to a blank string.```
  This is caused when a `.env` file has not been created or when the `.env` file
  is missing a variable used in the docker(-compose) file.

[laravel-docs]: https://laravel.com/docs/10.x
