#!/bin/bash

# 1. 执行各类检查
bash "${BASH_SOURCE[0]%/*}/phpunit.sh"
php "${BASH_SOURCE[0]%/*}/check-coverage.php" $2
bash "${BASH_SOURCE[0]%/*}/phpcs.sh"
bash "${BASH_SOURCE[0]%/*}/phpmd.sh"
bash "${BASH_SOURCE[0]%/*}/csslint.sh"
bash "${BASH_SOURCE[0]%/*}/stylelint.sh"
bash "${BASH_SOURCE[0]%/*}/eslint.sh"
bash "${BASH_SOURCE[0]%/*}/htmllint.sh"

# 2. 合并错误报告
error_file="error.txt"
count=`ls -1 reports/*.txt 2>/dev/null | wc -l`
if [ ${count} != 0 ]; then
  cat reports/*.txt > ${error_file}
fi
