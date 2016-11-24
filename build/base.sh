#!/bin/bash

set -e

changed_files=()
function get_changed_files () {
  # 1. 取出更改的文件
  results=$(git show --pretty="format:" --name-only HEAD~5..HEAD)
  if [[ -z "$results" ]];
  then
    echo "No changed files found."
    exit
  #else
  #  echo "Found changed files: ${results}"
  fi

  # 2. 获取符合规则的文件并去重
  for file in ${results}
  do
    # 忽略不存在的文件
    if [[ $2 && ! -e ${file} ]];
    then
      continue
    fi

    # vendor目录下只处理miaoxing的文件
    if [[ ${file} == vendor/* && ${file} != vendor/miaoxing/* ]];
    then
      continue
    fi

    # 筛选符合正则的文件
    if [[ ${file} =~ $1 ]];
    then
      changed_files+=(${file})
    fi

  done
  changed_files=($(printf "%s\n" "${changed_files[@]}" | sort -u))
  if [ -z "$changed_files" ];
  then
    echo "No changed files match $1"
    exit
  else
    echo "Found changed files: ${changed_files[@]}"
  fi
}

function join_by {
  local IFS="$1";
  shift;
  echo "$*";
}

function append_report {
  if [ -e "$1" ]; then
    detail=$(cat "$1")
    if [ "$detail" != "" ]; then
      printf "\n重现命令: $2\n\n$(printf %70s |tr " " "=")\n\n" >> $1
    fi
  fi
}
