#!/bin/bash

# Set Miaoxing coding standards
vendor/bin/phpcs --config-set installed_paths vendor/miaoxing/coding-standards
vendor/bin/phpcs -i

PATH=~/.composer/vendor/bin:$PATH
