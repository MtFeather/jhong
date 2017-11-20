#!/bin/bash

dnsfile="/etc/named/named.jhong.conf"

# 1. 如果不存在捷宏的設定檔，就建立他！
	[ ! -f $dnsfile ] && touch $dnsfile

# 2. 將舊檔案複製成 /dev/shm 裡面，然後才進行處理，效能較佳。
	rawfile=/etc/named.conf
	newfile=/dev/shm/jhong/named.conf
	[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong
	cp $rawfile $newfile

# 3. 將監聽的埠口打開才行，基本上，可以將 listen port 關閉即可
	sed -i 's/^[[:space:]]*listen-on port 53 { 127.0.0.1; };/\t\/\/listen-on port 53 { 127.0.0.1; };/g' $newfile
	sed -i 's/^[[:space:]]*listen-on-v6 port 53 { ::1; };/\t\/\/listen-on-v6 port 53 { ::1; };/g' $newfile
	sed -i 's/^[[:space:]]*allow-query[[:space:]]*{ localhost; };.*$/\tallow-query { any; };/g' $newfile
	sed -i 's/^[[:space:]]*recursion yes;.*$/\t\/\/recursion yes;/g' $newfile
	sed -i 's/^include "\/etc\/named.rfc1912.zones";/\/\/include "\/etc\/named.rfc1912.zones";/g' $newfile
	sed -i 's/^include "\/etc\/named.root.key";/\/\/include "\/etc\/named.root.key";/g' $newfile

# 4. 增加一個 acl 稱為 trustnetwork 來處理
	testing=$( grep 'acl trust' $newfile )
	if [ "$testing" == "" ]; then
		adding=$( grep -n '^options' $newfile | cut -d ':' -f 1)
		adding=$(( $adding - 1 ))
		sed -i "${adding}a \\\tacl trustnetwork { localhost; };" $newfile
	fi

	testing=$( grep 'allow-recursion' $newfile )
	if [ "$testing" == "" ]; then
		adding2=$( grep -n 'recursion' $newfile | cut -d ':' -f 1 | tail -n 1)
		sed -i "${adding2}a \\\tallow-recursion { trustnetwork; };" $newfile
	fi

# 5. 檢測看看有沒有加入捷宏的設定檔資訊了。
	testing=$( grep "${dnsfile}" $newfile )
	if [ "$testing" == "" ]; then
		echo "include \"$dnsfile\";" >> $newfile
	fi

# 5. 所以，修訂檔案了
	testing=$( diff $rawfile $newfile )
	if [ "$testing" != "" ]; then
		cp $newfile $rawfile
	fi
