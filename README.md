# DUS-I Portal

## Install

After creating a local git checkout of this repository, getting this project up
and running is a matter of:

Running the run.sh script

### TL;DR

Make sure you have PHP 8.2, composer, node and npm installed.

On Mac:

- First install [Homebrew](https://brew.sh).
- Run `brew install php@8.2`
- Run `brew install composer`

On Linux and Windows Subsystem for Linux use your package manager.

```shell
apt-get install php8.2-cli php8.2-bcmath php8.2-curl php8.2-gd php8.2-xml php8.2-zip
```

To run the backend applications you need to have docker installed. And
because we use a private docker registry you need to be logged in to the
registry. To do this run:

```docker login ghcr.io --username <username>```

In the prompt enter a personal access token with read:packages scope.
This token can be created in your
[GitHub account.](https://github.com/settings/tokens/new?scopes=read:packages&description=GitHub%20Container%20Registry%20Token)
Finally, if you want to test if you are logged in, run the following command:

```docker pull ghcr.io/minvws/nl-rdo-dusi-api-service```

To run the backend applications simply run:

```shell
./run.sh -c -i -m
```

To see a list of all available options run:

```shell
./run.sh -h
```

#### Github token

For installation steps a GitHub token is required, this token should have the
full repo control, and package read permissions.
[Generate new personal access token](https://github.com/settings/tokens/new?scopes=repo,read:packages&description=Composer+Token)

To implement this token the command
`composer config --global --auth github-oauth.github.com <token>`
can be run, replacing `<token>`.

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
