#!/bin/bash
num=1 
for file in `ls |sort -n|grep -i gif`
	do 
		mv $file $num.gif 
		num=`expr $num + 1` 
	done
