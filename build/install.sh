#!/bin/bash

# Set Miaoxing coding standards
vendor/bin/phpcs --config-set installed_paths vendor/miaoxing/coding-standards
vendor/bin/phpcs -i

# Make sure package.json exists
if [ ! -e "package.json" ]; then
  cp vendor/miaoxing/plugin/package.json package.json
fi
