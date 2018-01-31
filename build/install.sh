#!/bin/bash

# Install yarn
curl -o- -L https://yarnpkg.com/install.sh | bash -s -- --version 1.3.2
export PATH=$HOME/.yarn/bin:$PATH

# Install PHP-CS-Fixer
curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v1.12.1/php-cs-fixer.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer ~/.composer/vendor/bin/php-cs-fixer

# Set Miaoxing coding standards
vendor/bin/phpcs --config-set installed_paths vendor/miaoxing/coding-standards
vendor/bin/phpcs -i

PATH=~/.composer/vendor/bin:$PATH

yarn

yarn add -g htmllint-cli@0.0.5
