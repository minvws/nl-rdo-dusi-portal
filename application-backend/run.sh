#!/bin/bash

set -e

CLEAR=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-c|--clear-env] [-v|--verbose] [-h|--help]"
    echo "Options:"
    echo "  -c, --clear-env                 Copy the env files from the example files in each repository"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
    echo "  -m, --migrate                   Migrate database"
    echo "  -h, --help                      Display this help message"
    exit 1
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
        -c | --clear-env)
            CLEAR=true
            shift
            ;;
        -v | --verbose)
            set -x
            shift
            ;;
        -i | --install)
            INSTALL=true
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

if $INSTALL ; then
    composer install

    vendor/bin/sail up -d --remove-orphans
else
    vendor/bin/sail up -d --remove-orphans
fi

if $CLEAR ; then
    vendor/bin/sail artisan key:generate
fi

if $MIGRATE ; then
    vendor/bin/sail artisan migrate:fresh --path=vendor/minvws/dusi-application-model/database/migrations
fi
