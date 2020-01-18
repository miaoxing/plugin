#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行检查
standard=""
if [ -e "phpcs.xml.dist" ]; then
  standard="phpcs.xml.dist"
else
  standard="plugins/plugin/phpcs.xml.dist"
fi

report="reports/phpcs.txt"
command="vendor/bin/phpcs --standard=${standard} --report-file=${report} -v ."
info "${command}";

${command} || true

# 2. 移除最后两行空白,并附加命令到报告中
sed -e :a -e '$d;N;2,2ba' -e 'P;D' "${report}" > temp.txt
mv temp.txt "${report}"
append_report "${report}" "${command}"
