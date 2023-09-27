#!/bin/bash

set -e

FORCE=false
IGNORE_PLATFORM_REQS=false
INSTALL=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-v|--verbose] [-h|--help]"
    echo "Options:"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
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

    npm install
    npm run build
fi
