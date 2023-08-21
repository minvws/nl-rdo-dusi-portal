#!/bin/bash

set -xe

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

$BASEDIR/start.sh

packages=( subsidy-model application-model subsidy-admin-api user-admin-api application-api application-backend assessment-api )
for package in "${packages[@]}"
do
  cd $BASEDIR/$package
  ./lint.sh
done