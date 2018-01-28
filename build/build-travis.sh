#!/bin/bash

set -e

# 1. 如果上一步是自动修复代码,则不用再做一遍
message=$(git log -1 --pretty=%B)
if [[ "$message" == *"[skip fix]"* ]]; then
  echo "skip fix"
  exit 0
fi

# 2. 执行各类检查
mkdir -p reports
PATH=~/.composer/vendor/bin:$PATH

php "${BASH_SOURCE[0]%/*}/install.php"
php "${BASH_SOURCE[0]%/*}/create-tests.php"

if [ "$TRAVIS_PHP_VERSION" == "5.6" ]; then
  bash "${BASH_SOURCE[0]%/*}/phpunit.sh" $@
  bash "${BASH_SOURCE[0]%/*}/phpcs.sh"
  bash "${BASH_SOURCE[0]%/*}/phpmd.sh"
  bash "${BASH_SOURCE[0]%/*}/stylelint.sh"
  bash "${BASH_SOURCE[0]%/*}/eslint.sh"
  bash "${BASH_SOURCE[0]%/*}/htmllint.sh"
  php "${BASH_SOURCE[0]%/*}/check-migration.php"
  php "${BASH_SOURCE[0]%/*}/check-inline-script.php" $3
else
  bash "${BASH_SOURCE[0]%/*}/phpunit.sh"
fi

# 2. 合并错误报告
error_file="error.txt"
count=`ls -1 reports/*.txt 2>/dev/null | wc -l`
if [ ${count} != 0 ]; then
  cat reports/*.txt > ${error_file}
fi

# 3. 创建issues

# Check if error file not empty
if [ -e "$error_file" ]; then
  detail=$(cat "$error_file")
else
  echo "error file \"$error_file\" not found"
  exit 0
fi

if [ "$detail" == "" ]; then
  echo "error file is empty"
  exit 0
fi

# 如果允许失败则不创建issue
allow_failures=("7.0" "7.1" "7.2" "nightly")
if [[ " ${allow_failures[@]} " =~ " ${TRAVIS_PHP_VERSION} " ]]; then
  exit 1
fi

# Build issue title
message=$(git log -1 --pretty=%B "$TRAVIS_COMMIT")
title="[$(date +%y-%m-%d)]Build failed: $message - $TRAVIS_PHP_VERSION"
assignee=$(git log -1 --pretty=%an "$TRAVIS_COMMIT")
body="Status: failed

Author: $assignee
Message: $message

"

body+="Detail:
\`\`\`
$detail
\`\`\`
"

body+="

View the changeset: $TRAVIS_REPO_SLUG@$TRAVIS_COMMIT

View the full build log and details: https://travis-ci.org/$TRAVIS_REPO_SLUG/jobs/$TRAVIS_JOB_ID"
body=${body//\\/\\\\}
body=${body//\"/\\\"}
body=${body//\	/\\\t}
body=${body//
/\\\n}
body=`echo "${body}" | perl -pe 's/\e\[?.*?[\@-~]//g'` # 过滤颜色

data="{\"title\":\"$title\",\"body\":\"$body\",\"assignees\":[\"$assignee\"],\"labels\":[\"task\"]}"
echo "$data"

GITHUB_ISSUE_NOTIFY_REPO=${GITHUB_ISSUE_NOTIFY_REPO:-twinh/test}
curl -H "Authorization: token $GITHUB_ISSUE_NOTIFY_TOKEN" -d "$data" \
"https://api.github.com/repos/$GITHUB_ISSUE_NOTIFY_REPO/issues"

# 4. 返回构建失败
exit 1;
