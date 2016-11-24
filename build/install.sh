#!/bin/bash

# Install PHPMD with --ignore-violations-on-exit flag support
composer global require "phpmd/phpmd:dev-master#f2467d1729dc12af79a8a834417ed15e7a14f485@dev"

composer global config repositories.phpmd-extension git https://github.com/mi-schi/phpmd-extension.git \
    && composer global require mi-schi/phpmd-extension:4.*

# Install PHP-CS-Fixer
curl -L https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v1.12.1/php-cs-fixer.phar -o php-cs-fixer \
    && chmod a+x php-cs-fixer \
    && mv php-cs-fixer ~/.composer/vendor/bin/php-cs-fixer

# Install PHP_CodeSniffer, Miaoxing Coding standards
composer global require miaoxing/coding-standards:0.9.1 \
    && ~/.composer/vendor/bin/phpcs --config-set installed_paths ~/.composer/vendor/miaoxing/coding-standards \
    && ~/.composer/vendor/bin/phpcs -i

PATH=~/.composer/vendor/bin:$PATH

# Install eslint
npm install -g eslint babel-eslint

# Install htmllint-cli
npm install -g htmllint-cli

# Install csslint
npm install -g csslint
