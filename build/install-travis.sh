#!/bin/bash

set -e

if [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
  bash "${BASH_SOURCE[0]%/*}/install.sh"
fi

if [ "$TRAVIS_PHP_VERSION" == "7.0" ]; then
  composer global require phpunit/phpunit:5.7
fi
