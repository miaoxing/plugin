#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 忽略没有视图目录的情况,否则csslint会返回错误
dir="resources/views"
if [ ! -d "${dir}" ]; then
  echo "No ${dir} directory, exit."
  exit;
fi

# 2. 执行命令
config=""
if [ ! -e ".htmllintrc" ]; then
  config=" --rc=vendor/miaoxing/plugin/.htmllintrc"
fi

files=`find ${dir} -type f`
files=${files//
/ }
report="reports/htmllint.txt"
command="htmllint$config ${files[@]}"
info "${command}";

${command} | tee ${report}

# 3. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi
