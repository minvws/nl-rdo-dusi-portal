#!/bin/bash

set -xe
vendor/bin/phpcs
vendor/bin/phpmd src/ text ruleset.phpmd.xml
vendor/bin/psalm --no-cache
vendor/bin/phpstan analyse --memory-limit=-1
docker run --name shared-test-postgres -v "$(pwd)/tests/Scripts/init.sql:/docker-entrypoint-initdb.d/10-create-testing-database.sql" -e POSTGRES_PASSWORD=postgres -p 54322:5432 -d postgres || docker start shared-test-postgres ||  echo "Unable to start postgres container, it may already be running"
sleep 3
vendor/bin/phpunit