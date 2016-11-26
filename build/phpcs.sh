#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
report="reports/phpcs.txt"
command="phpcs --extensions=php --standard=Miaoxing --report-file=${report} -v ."
info "${command}";

${command} || true

# 2. 移除最后两行空白,并附加命令到报告中
< "${report}" tail -n +2 | tail -r > temp.txt
mv temp.txt "${report}"
append_report "${report}" "${command}"
