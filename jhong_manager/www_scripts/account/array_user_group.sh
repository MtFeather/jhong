#!/bin/bash

# 取得使用者陣列
# user1: 帳號名稱
# user3: UID
# user4: primary GID
# user5: user name
# user6: homedirtory
# user7: shell
# group1: groupname
# group2: groupid
# group3: secondray user name

if [ "$1" == "user" -o "$1" == "all" ]; then
totaluser=$(wc -l /etc/passwd | cut -d ' ' -f1 )
for i in $(seq 1 ${totaluser} )
do
	userinfo=$( cat /etc/passwd | sort | sed -n "${i}p" )
	user1[${i}]=$(echo $userinfo | cut -d ':' -f 1)
	user3[${i}]=$(echo $userinfo | cut -d ':' -f 3)
	user4[${i}]=$(echo $userinfo | cut -d ':' -f 4)
	user5[${i}]=$(echo $userinfo | cut -d ':' -f 5)
	user6[${i}]=$(echo $userinfo | cut -d ':' -f 6)
	user7[${i}]=$(echo $userinfo | cut -d ':' -f 7)
	#echo "${user1[${i}]} .. ${user3[${i}]} .. ${user4[${i}]} .. ${user5[${i}]} .. ${user6[${i}]} .. ${user7[${i}]} .."
done
fi

if [ "$1" == "group" -o "$1" == "all" ]; then
totalgroup=$(wc -l /etc/group | cut -d ' ' -f1 )
for i in $(seq 1 ${totalgroup} )
do
	groupinfo=$( cat /etc/group | sort | sed -n "${i}p" )
	group1[${i}]=$(echo $groupinfo | cut -d ':' -f 1)
	group3[${i}]=$(echo $groupinfo | cut -d ':' -f 3)
	group4[${i}]=$(echo $groupinfo | cut -d ':' -f 4)
	#echo "${group1[${i}]} .. ${group3[${i}]} .. ${group4[${i}]} .. "
done
fi
