#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行命令

config=""
if [ ! -e ".htmllintrc" ]; then
  config=" --rc=vendor/miaoxing/plugin/.htmllintrc"
fi

files=`find resources/views -type f`
files=${files//
/ }
report="reports/htmllint.txt"
command="htmllint$config ${files[@]}"
info "${command}";

${command} | tee ${report}

# 2. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi
