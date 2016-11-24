#!/bin/bash

set -e

if [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
  source ~/.nvm/nvm.sh;
  nvm install node
  bash "${BASH_SOURCE[0]%/*}/install.sh"
fi
