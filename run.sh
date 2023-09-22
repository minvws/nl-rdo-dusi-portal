#!/bin/bash

set -e

ARGUMENTS="$@"

DOWN=false
FORCE=false
INSTALL=false
OTHER_ARGUMENT=false

# Function to display script usage
function display_usage() {
    echo "Usage: $0 [-c|--clear-env] [-v|--verbose] [-i|--install] [-m|--migrate] [-h|--help]"
    echo "Options:"
    echo "  -c, --clear-env                 Copy the env files from the example files in each repository"
    echo "  -v, --verbose                   Print the commands that are executed"
    echo "  -i, --install                   Install packages"
    echo "  -f, --force                     Force override of installed packages"
    echo "      --ignore-platform-reqs      Ignore platform requirements during composer install"
    echo "  -m, --migrate                   Migrate database"
    echo "  -d, --down                      Stop the applications and remove docker containers"
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
        -d | --down)
            DOWN=true
            shift
            ;;
        -i | --install)
            INSTALL=true
            shift
            ;;
        -f | --force)
            FORCE=true
            shift
            ;;
        * )
            OTHER_ARGUMENT=true
            shift
            ;;
    esac
done

if "$DOWN" && ( "$INSTALL" || "$OTHER_ARGUMENT" ) ; then
    echo "Down only works without other arguments. Please run the script again with only the down option."
    exit 1
fi

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( bridge shared application-backend user-admin-api subsidy-admin-api application-api assessment-api )
for package in "${packages[@]}"
do
  printf "\033[1;94mExecuting run.sh for ${package}\033[0m\n"
  cd $BASEDIR/$package
  ./run.sh $ARGUMENTS
  printf "\n"
done

if "$DOWN" ; then
    echo "Stopped docker applications"
    exit 0
fi

cp "$BASEDIR/application-backend/secrets/public.key" "$BASEDIR/application-api/secrets/public.key"
cp "$BASEDIR/application-backend/secrets/public.key" "$BASEDIR/assessment-api/secrets/public.key"
cp "$BASEDIR/application-backend/secrets/pki/issued/softhsm^SoftHSMLabel^*=create,destroy,use,import.crt" "$BASEDIR/assessment-api/secrets/softhsm.crt"
cp "$BASEDIR/application-backend/secrets/pki/private/softhsm^SoftHSMLabel^*=create,destroy,use,import.key" "$BASEDIR/assessment-api/secrets/softhsm.key"

echo "Creating user:"
cd "$BASEDIR/user-admin-api"

vendor/bin/sail artisan organisation:create "DUS-I"
vendor/bin/sail artisan admin:create user@example.com user password

echo "Log user in with: user@example.com password"
cd "$BASEDIR"
