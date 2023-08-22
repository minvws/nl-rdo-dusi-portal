#!/bin/bash

set -e

ARGUMENTS="$@"

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-c|--clear-env] [-v|--verbose] [-i|--install] [-m|--migrate] [-h|--help]"
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
        -v | --verbose)
            set -x
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

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( serialisation-model subsidy-model application-model user-admin-api subsidy-admin-api application-backend application-api assessment-api )
for package in "${packages[@]}"
do
  cd $BASEDIR/$package
  ./run.sh $ARGUMENTS
done

echo "Initialisation is finished, listening for incoming applications:"
cd "$BASEDIR/application-backend"
vendor/bin/sail artisan rabbitmq:consume