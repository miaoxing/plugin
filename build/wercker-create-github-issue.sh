#!/usr/bin/env bash

echo 'Build' $WERCKER_RESULT;

if [ $WERCKER_RESULT = 'failed' ] ; then
  body=${WERCKER_FAILED_STEP_MESSAGE//\'/\\\'}
  body=${body//\"/\\\"}
  body=${body//
/\\\n}
  curl -i -H "Authorization: token $GITHUB_ISSUE_TOKEN" \
  -d "{\"title\": \"Failed: $WERCKER_FAILED_STEP_DISPLAY_NAME\",\"body\":\"$body\",\"labels\": [\"task\"]}" \
  https://api.github.com/repos/$GITHUB_ISSUE_REPO/issues
fi
