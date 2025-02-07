#!/bin/bash

set -xe

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( shared subsidy-admin-api application-api application-backend assessment-api user-admin-api bridge )
for package in "${packages[@]}"
do
  echo "Run linter for $package"
  cd $BASEDIR/$package
  ./lint.sh
done