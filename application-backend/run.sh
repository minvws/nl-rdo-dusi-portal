#!/bin/bash

set -e

CLEAR=false
DOWN=false
FORCE=false
INSTALL=false
UPDATE=false
IGNORE_PLATFORM_REQS=false
MIGRATE=false
NOPROMPTS=true

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-c|--clear-env] [-v|--verbose] [-i|--install] [-u|--update] [-m|--migrate] [-h|--help]"
    echo "Options:"
    echo "  -c, --clear-env                 Copy the env files from the example files in each repository"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
    echo "  -u, --update                    Update packages"
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
            NOPROMPTS=false
            shift
            ;;
        -i | --install)
            INSTALL=true
            shift
            ;;
        -u | --update)
            UPDATE=true
            shift
            ;;
        --ignore-platform-reqs)
            IGNORE_PLATFORM_REQS=true
            shift
            ;;
        -f | --force)
            FORCE=true
            shift
            ;;
        -m | --migrate)
            MIGRATE=true
            shift
            ;;
        -h | --help)
            display_usage
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
fi

if $UPDATE ; then
    if $IGNORE_PLATFORM_REQS ; then
        composer update --ignore-platform-req=ext-redis --ignore-platform-req=ext-sodium
    else
        composer update
    fi
    exit 0
fi

vendor/bin/sail up -d --remove-orphans

if $CLEAR ; then
    vendor/bin/sail artisan key:generate
fi

if $MIGRATE ; then
    vendor/bin/sail artisan migrate:fresh
fi

if $INSTALL ; then
    echo "Waiting for the HSM API to boot..."
    sleep 5 # give the HSM API some time to boot
    vendor/bin/sail artisan hsm:local-init --no-prompts=$NOPROMPTS
fi
