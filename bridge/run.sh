#!/bin/bash

set -e

FORCE=false
IGNORE_PLATFORM_REQS=false
INSTALL=false
UPDATE=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-v|--verbose] [-i|--install] [-u|--update] [-h|--help]"
    echo "Options:"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
    echo "  -u, --update                    Update packages"
    echo "      --ignore-platform-reqs      Ignore platform requirements during composer install"
    echo "  -f, --force                     Force override of installed packages"
    echo "  -h, --help                      Display this help message"
    exit 1
}

# Parse command line arguments
while [[ $# -gt 0 ]]; do
    case $1 in
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
        * )
            shift
            ;;
    esac
done

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

