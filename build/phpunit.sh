#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpunit.txt"
base_command="phpunit --verbose --stderr"
command="${base_command} --coverage-clover build/logs/clover.xml --coverage-text"
info "${command}";

${command} 2>&1 | tee ${report}
code=${PIPESTATUS[0]}

# 2. 检查覆盖率
if [[ $1 == '--coverage' ]]; then
  php "${BASH_SOURCE[0]%/*}/check-coverage.php" $2
fi

# 3. 调整报告
if [[ ${code} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${base_command}"
fi
