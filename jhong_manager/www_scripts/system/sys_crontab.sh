#!/bin/bash

PATH=/bin:/sbin:/usr/bin:/usr/sbin
source="../source/source.sh"
filename="/etc/cron.d/jhong"

[ ! -e "${filename}" ] &&  touch "${filename}"
line=$(wc -l ${filename} | awk '{print $1}')
if [ "$line" -le 0 ]; then
	exit 0
fi
for i in $(seq 1 ${line} ) 
do
	jobcontent=$( sed -n "${i}p" ${filename})
	min=$(  echo "${jobcontent}" | cut -d ' ' -f 1)
	hr=$(   echo "${jobcontent}" | cut -d ' ' -f 2)
	day=$(  echo "${jobcontent}" | cut -d ' ' -f 3)
	mon=$(  echo "${jobcontent}" | cut -d ' ' -f 4)
	week=$( echo "${jobcontent}" | cut -d ' ' -f 5)
	cmd=$(  echo "${jobcontent}" | cut -d ' ' -f 6-)
	echo "<tr class='text-center'>
		<td><b>$mon</b></td>
		<td><b>$day</b></td>
		<td><b>$week</b></td>
		<td><b>$hr</b></td>
		<td><b>$min</b></td>
		<td><b>$cmd</b></td>
		<td><a href='?delnu=${i}' class='btn btn-primary btn-sm' OnClick='return delfunc()'>刪除</a></td></tr>"
done

