#!/bin/bash

set -e

if [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
  # Install yarn
  curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version 1.3.2
  export PATH=$HOME/.yarn/bin:$PATH

  # Install dependencies
  yarn

  bash "${BASH_SOURCE[0]%/*}/install.sh"
fi

if [ "$TRAVIS_PHP_VERSION" == "7.0" ] || [ "$TRAVIS_PHP_VERSION" == "7.1" ]; then
  composer global require phpunit/phpunit:5.7
fi
