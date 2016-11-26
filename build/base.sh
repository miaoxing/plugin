#!/bin/bash

set -e

mkdir -p reports

function append_report {
  if [ -e "$1" ]; then
    detail=$(cat "$1")
    if [ "$detail" != "" ]; then
      printf "\n重现命令: $2\n\n$(printf %70s |tr " " "=")\n\n" >> $1
    fi
  fi
}

function info {
  echo -e "\033[32m$1\033[0m";
}
