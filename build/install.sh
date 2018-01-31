#!/bin/bash

# Install yarn
curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version 1.3.2
export PATH=$HOME/.yarn/bin:$PATH

# Set Miaoxing coding standards
vendor/bin/phpcs --config-set installed_paths vendor/miaoxing/coding-standards
vendor/bin/phpcs -i

PATH=~/.composer/vendor/bin:$PATH

yarn

yarn add htmllint-cli@0.0.5 -g
