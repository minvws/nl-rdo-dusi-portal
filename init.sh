#!/bin/bash

set -xe

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

packages=( subsidy-model application-model subsidy-admin-api user-admin-api application-api application-backend assessment-api )
for package in "${packages[@]}"
do
  cd $BASEDIR/$package
  ./init.sh
done