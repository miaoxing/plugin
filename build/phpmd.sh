#!/bin/bash

set -e

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查

config="phpmd.xml"
if [ ! -e "phpmd.xml" ]; then
  config="vendor/miaoxing/plugin/build/phpmd-for-vendors.xml"
fi

report="reports/phpmd.txt"
command="phpmd . text $config \
--reportfile-text ${report} \
--exclude vendor \
--ignore-violations-on-exit"
echo "${command}";

${command}
code=$?

# 2. 附加命令到报告中
append_report "${report}" "${command}"

exit ${code}
