#!/bin/bash

PATH=/bin:/sbin:/usr/bin:/usr/sbin
source="../source/source.sh"
source ./array_user_group.sh user

groupname=${1}		# 群組名稱
groupcheck=${2}		# 顯示的內容，主要有 1)作為主要支援的用戶 2)有加入為次要的 3)沒加入的 三種狀態

# 先取得這個群組的資訊
groupid=$(    grep "^${groupname}:" /etc/group | cut -d ':' -f 3 )
groupusers=$( grep "^${groupname}:" /etc/group | cut -d ':' -f 4 )
#groupusers=$( grep "^${groupname}:" /etc/group | cut -d ':' -f4 | sed 's/,/\n/g' | sort)

# 列出作為主要群組支援的用戶
if [ "$groupcheck" == "primary" ]; then
	for i in $( seq 1 $totaluser )
	do
		if [ "${user4[${i}]}" == "${groupid}" -a "${user3[${i}]}" -ge 1000 -a "${user3[${i}]}" -le 60000 ]; then
			userprimary="$userprimary ${user1[${i}]}"
		fi
	done
	if [ "$userprimary" != "" ]; then
		echo $userprimary | sed 's/ /,/g'
	else
		echo ""
	fi
	exit
fi

# 列出未在此群組中的帳號
if [ "$groupcheck" == "not" ]; then
	for i in $( seq 1 $totaluser )
	do
		checksec=$( echo $groupusers | sed 's/,/\n/g' | grep "^${user1[${i}]}$" )
		if [ "${user4[${i}]}" != "${groupid}" -a "${user3[${i}]}" -ge 1000 -a "${user3[${i}]}" -le 60000 -a "${checksec}" == "" ]; then
			usernot="$usernot ${user1[${i}]}"
		fi
	done
	if [ "$usernot" != "" ]; then
		echo $usernot | sed 's/ /,/g'
	else
		echo ""
	fi
	exit
fi

# 列出可以移除此群組的帳號
if [ "$groupcheck" == "yes" ]; then
	for i in $( seq 1 $totaluser )
	do
		checksec=$( echo $groupusers | sed 's/,/\n/g' | grep "^${user1[${i}]}$" )
		if [ "${user4[${i}]}" != "${groupid}" -a "${user3[${i}]}" -ge 1000 -a "${user3[${i}]}" -le 60000 -a "${checksec}" != "" ]; then
			useryes="$useryes ${user1[${i}]}"
		fi
	done
	if [ "$useryes" != "" ]; then
		echo $useryes | sed 's/ /,/g'
	else
		echo ""
	fi
	exit
fi

