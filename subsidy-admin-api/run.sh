#!/bin/bash

set -e

CLEAR=false
DOWN=false
FORCE=false
IGNORE_PLATFORM_REQS=false
INSTALL=false
MIGRATE=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-c|--clear-env] [-v|--verbose] [-i|--install] [-m|--migrate] [-h|--help]"
    echo "Options:"
    echo "  -c, --clear-env                 Copy the env files from the example files in each repository"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
    echo "      --ignore-platform-reqs      Ignore platform requirements during composer install"
    echo "  -f, --force                     Force override of installed packages"
    echo "  -m, --migrate                   Migrate database"
    echo "  -d, --down                      Stop the applications and remove docker containers"
    echo "  -h, --help                      Display this help message"
    exit 1
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -d | --down)
            DOWN=true
            shift
            ;;
        -c | --clear-env)
            CLEAR=true
            shift
            ;;
        -v | --verbose)
            set -x
            shift
            ;;
        -h | --help)
            display_usage
            ;;
        -i | --install)
            INSTALL=true
            shift
            ;;
        --ignore-platform-reqs)
            IGNORE_PLATFORM_REQS=true
            shift
            ;;
        -m | --migrate)
            MIGRATE=true
            shift
            ;;
        * )
            shift
            ;;
    esac
done

if $CLEAR ; then
  rm -f .env
fi
if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if $DOWN ; then
    if [ -f "vendor/bin/sail" ]; then
        vendor/bin/sail down --remove-orphans
    else
        echo "Composer packages not installed."
    fi
    exit 0
fi

if $INSTALL ; then
    if $FORCE ; then
        rm -rf ./vendor
    fi

    if $IGNORE_PLATFORM_REQS ; then
        composer install --ignore-platform-reqs
    else
        composer install
    fi

    vendor/bin/sail up -d --remove-orphans

    # Install gmp to accellerate encryption/decryption
    vendor/bin/sail root-shell -c 'apt-get update; apt-get --yes install php8.2-gmp'
    vendor/bin/sail restart
else
    vendor/bin/sail up -d --remove-orphans
fi

if $CLEAR ; then
    vendor/bin/sail artisan key:generate
fi

if $MIGRATE ; then
    vendor/bin/sail artisan db:seed --class="MinVWS\\DUSi\\Subsidy\\Admin\\API\\Database\\Seeders\\DatabaseSeeder"
fi
