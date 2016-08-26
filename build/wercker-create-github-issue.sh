#!/usr/bin/env bash

echo 'Build' $WERCKER_RESULT;
echo 'Started by' $WERCKER_STARTED_BY;

if [ $WERCKER_RESULT = 'failed' ] ; then
  commit=$(git log -1 --pretty=%B)
  echo $(git log -1 --pretty=%cn)
  assignee='twinh'
  body="出错步骤: $WERCKER_FAILED_STEP_DISPLAY_NAME - $WERCKER_FAILED_STEP_MESSAGE
提交信息: $commit $WERCKER_GIT_OWNER/$WERCKER_GIT_REPOSITORY@$WERCKER_GIT_COMMIT
构建地址: $WERCKER_RUN_URL"
  body=${body//\'/\\\'}
  body=${body//\"/\\\"}
  body=${body//
/\\\n}
  echo $body
  curl -i -H "Authorization: token $GITHUB_ISSUE_TOKEN" \
  -d "{\"title\": \"Build failed: $commit\",\"body\":\"$body\",\"assignees\":[\"$assignee\"],\"labels\": [\"task\"]}" \
  https://api.github.com/repos/$GITHUB_ISSUE_REPO/issues --verbose
fi
