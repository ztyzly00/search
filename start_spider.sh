#!/bin/bash

str=$"\n"
curr_dir=$(cd "$(dirname "$0")"; pwd)
for((i=1;i<=6;i++));do
    nohup php $curr_dir/spider_start.php $i 2>/dev/null &
    sstr=$(echo -e $str)
done
