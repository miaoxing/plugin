#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查

config="phpmd.xml"
if [ ! -e "phpmd.xml" ]; then
  config="plugins/plugin/build/phpmd-for-vendors.xml"
fi

report="reports/phpmd.txt"
command="vendor/bin/phpmd . text $config \
--reportfile-text ${report} \
--exclude vendor,src/Lib,public/comps,public/libs \
--ignore-violations-on-exit"
info "${command}";

${command} || true

# 2. 附加命令到报告中
append_report "${report}" "${command}"
