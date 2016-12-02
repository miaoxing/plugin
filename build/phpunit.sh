#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpunit.txt"
coverage_report="coverage.txt"
base_command="phpunit --verbose --stderr"
command="${base_command} --colors=never --coverage-clover build/logs/clover.xml --coverage-text"
info "${command}";

${command} 2>&1 | tee ${report}

# 2. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${base_command}"
fi
