#!/bin/bash

set -xe

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( bridge serialisation-model subsidy-model application-model subsidy-admin-api user-admin-api application-api application-backend assessment-api )
for package in "${packages[@]}"
do
  echo "Run linter for $package"
  cd $BASEDIR/$package
  ./lint.sh
done