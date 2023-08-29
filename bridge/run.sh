#!/bin/bash

set -e

INSTALL=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-v|--verbose] [-h|--help]"
    echo "Options:"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
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
        * )
            shift
            ;;
    esac
done

if $INSTALL ; then
    composer install
fi
