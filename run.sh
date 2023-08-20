#!/bin/bash

set -xe

SCRIPT=$(readlink -f $0)
BASEDIR=`dirname $SCRIPT`

$BASEDIR/start.sh

cd $BASEDIR/subsidy-admin-api
vendor/bin/sail artisan migrate:fresh
vendor/bin/sail artisan db:seed --class="MinVWS\DUSi\Subsidy\Admin\API\Database\Seeders\DatabaseSeeder"

cd $BASEDIR/application-backend
vendor/bin/sail artisan migrate:fresh --path vendor/minvws/dusi-application-model/database/migrations

cd $BASEDIR/application-backend
vendor/bin/sail artisan rabbitmq:consume
