#!/bin/bash

# 1. 如果上一步是自动修复代码,则不用再做一遍
message=$(git log -1 --pretty=%B)
if [[ "$message" == *"[skip fix]"* ]]; then
  echo "skip fix"
  exit 0
fi

# 2. 执行各类检查
php "${BASH_SOURCE[0]%/*}/install.php"
php "${BASH_SOURCE[0]%/*}/create-tests.php"
bash "${BASH_SOURCE[0]%/*}/phpunit.sh" $@
bash "${BASH_SOURCE[0]%/*}/phpcs.sh"
bash "${BASH_SOURCE[0]%/*}/phpmd.sh"
bash "${BASH_SOURCE[0]%/*}/csslint.sh"
bash "${BASH_SOURCE[0]%/*}/stylelint.sh"
bash "${BASH_SOURCE[0]%/*}/eslint.sh"
bash "${BASH_SOURCE[0]%/*}/htmllint.sh"
php "${BASH_SOURCE[0]%/*}/check-migration.php"

# 3. 合并错误报告
error_file="error.txt"
count=`ls -1 reports/*.txt 2>/dev/null | wc -l`
if [ ${count} != 0 ]; then
  cat reports/*.txt > "$error_file"
  detail=$(cat "$error_file")
  if [[ "$detail" = "" ]]; then
    echo "build passed"
  else
    echo "build failed"
    exit 1
  fi
fi
