#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 忽略没有CSS目录的情况,否则csslint会返回错误
if [ ! -d "public/css" ]; then
  echo "No public/css directory, exit."
  exit 0;
fi

if [ -e ".csslintrc" ]; then
  config=""
else
  config=" --config=vendor/miaoxing/plugin/.csslintrc"
fi

# 2. 执行命令
report="reports/csslint.txt"
command="csslint$config public"
echo "${command}";

${command} | tee ${report}

# 3. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi

