#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpunit.txt"
command="phpunit --verbose --stderr --coverage-clover build/logs/clover.xml"
echo "${command}";

${command} | tee ${report}

# 2. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi
