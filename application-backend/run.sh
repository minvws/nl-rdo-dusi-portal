#!/bin/bash
set -e

composer install
vendor/bin/sail up -d
