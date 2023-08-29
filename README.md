# DUS-I Portal

## Install

After creating a local git checkout of this repository, getting this project up
and running is a matter of:

Running the run.sh script

### TL;DR

Make sure you have PHP 8.2 and composer installed.

On Mac:

- First install [Homebrew](https://brew.sh).
- Run `brew install php@8.2`
- Run `brew install composer`

On Linux and Windows Subsystem for Linux use your package manager.

To run the backend applications simply run:

```./run.sh -c -i -m```

To see a list of all available options run:

```./run.sh -h```

### Frontend

At this time the Vue application frontend is separate from the web API. To
start the Vue application follow the readme on the
[portal-web-frontend](https://github.com/minvws/nl-rdo-dusi-portal-web-frontend)
repository.

### Development

This application has been developed in Laravel, please see the
[Laravel docs][laravel-docs]
for Laravel specific details.

### Laravel Sail / Docker compose

Docker compose is wrapped using Laravel Sail. Instead of using `docker compose`
please use `vendor/bin/sail` to make
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
