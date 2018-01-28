#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 忽略没有CSS目录的情况,否则stylelint会返回错误
dir="public/css"
if [ ! -d "${dir}" ]; then
  echo "No ${dir} directory, exit."
  exit 0;
fi

if [ -e ".stylelintrc.json" ]; then
  config=""
else
  config=" --config vendor/miaoxing/plugin/.stylelintrc.json"
fi

# 2. 执行命令
files=`find ${dir} -type f`
files=${files//
/ }
report="reports/stylelint.txt"
command="stylelint$config ${files[@]}"
info "${command}";

${command} | tee ${report}

# 3. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi
