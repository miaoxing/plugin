#!/bin/bash

source "${BASH_SOURCE[0]%/*}/base.sh"

# 1. 执行命令

# 如果没有忽略文件，拷贝默认配置
copyIgnoreFile=false
if [ ! -e ".eslintignore" ]; then
  cp "vendor/miaoxing/plugin/.eslintignore" ".eslintignore"
  copyIgnoreFile=true
fi

config=""
if [ ! -e ".eslintrc.json" ]; then
  config=" --config=vendor/miaoxing/plugin/.eslintrc.json"
fi

report="reports/eslint.txt"
command="eslint$config ."
info "${command}";

${command} | tee ${report}

# 2. 调整报告
if [[ ${PIPESTATUS[0]} == 0 ]]; then
  # 如果检测没有问题,删除报告
  rm -f ${report}
else
  append_report "${report}" "${command}"
fi

if [ copyIgnoreFile ]; then
  rm ".eslintignore"
fi
