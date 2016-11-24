#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpcs.txt"
command="phpcs --standard=Miaoxing --report-file=${report} ."
echo "${command}";

${command}
code=$?

# 2. 移除最后两行空白,并附加命令到报告中
head -n -2 "${report}" > temp.txt ; mv temp.txt "${report}"
append_report "${report}" "${command}"

# 3. 如果检测到问题,仍然认为是运行成功
if [[ code -eq 1 ]]; then
  exit 0
else
  exit ${code}
fi
