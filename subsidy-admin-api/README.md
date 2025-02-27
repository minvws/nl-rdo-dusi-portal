# Form Admin Web

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

# 2. Connect to portal
By default the admin panel is accessible at http://localhost:8002
```

## Usage

## Development

This application has been developed in Laravel, please see the
[Laravel docs][laravel-docs]
for Laravel specific details.

### Sail

- Before running `sail up`, be sure to create and edit a `.env` file .

- Whenever something changes in the docker setup, don't forget to re-build the
  containers:

  ```sh
  sail up --build --remove-orphans
  ```

- A script that checks the develop environment is run when `sail up` is run.
This script will report any problems and try to make suggestions on how to
resolve things.
These suggestions can usually be run inside the Docker container (using
`sail exec laraval.test some-command`).

- Docker-compose might show this warning:
  ```WARNING: The [...] variable is not set. Defaulting to a blank string.```
  This is caused when a `.env` file has not been created or when the `.env` file
  is missing a variable used in the docker(-compose) file.

[laravel-docs]: https://laravel.com/docs/10.x
