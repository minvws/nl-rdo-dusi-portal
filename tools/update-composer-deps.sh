#!/bin/bash

set -e

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( bridge shared application-backend user-admin-api subsidy-admin-api application-api assessment-api )
for package in "${packages[@]}"
do
  printf "\033[1;94mExecuting package updates for ${package}\033[0m\n"
  cd $BASEDIR/../$package
  ./run.sh -u
  printf "\n"
done
