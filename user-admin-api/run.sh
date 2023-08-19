#!/bin/bash
set -e

composer install
npm run build
vendor/bin/sail up -d
