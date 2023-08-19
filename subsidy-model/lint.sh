#!/bin/bash

set -xe
vendor/bin/phpcs
vendor/bin/phpmd src/ text ruleset.phpmd.xml
vendor/bin/psalm --no-cache
vendor/bin/phpstan analyse --memory-limit=-1
docker run --name subsidy-model-test-postgres -e POSTGRES_PASSWORD=postgres -p 5432:5432 -d postgres || echo "Unable to start postgres container, it may already be running"
vendor/bin/phpunit