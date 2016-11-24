#!/bin/bash

if [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
  nvm install node
  bash "${BASH_SOURCE[0]%/*}/install.sh"
fi
