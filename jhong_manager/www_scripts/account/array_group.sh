#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin
source="../source/source.sh"

# 取得使用者與群組的陣列資訊
source ./array_user_group.sh all

# Get the 1. group name 2. group ID (check >=1000 and <=60000 and 3. users who join the group.
for i in $( seq 1 $totalgroup )
do
	usergroup=""
	if [ "${group3[${i}]}" -ge 1000 ] && [ "${group3[${i}]}" -le 60000 ]; then
		for j in $( seq 1 $totaluser )
		do
			if [ "${user4[${j}]}" == "${group3[${i}]}" ]; then
				users=$(echo "${user1[${j}]}" | sed 's/^bk_//g' )
				if [ "$checking" == "" ]; then
					usergroup="${usergroup} ${users}"
				fi
			fi
		done
		if [ "${usergroup}" != "" ]; then
			usergroup="${usergroup} ${group4[${i}]}"
		else
			usergroup=${group4[${i}]}
		fi
		usergroup=$( echo $(echo "${usergroup}"| sed 's/,/ /g' |sed 's/ /\n/g' | sort | uniq ) | sed 's/ /,/g' )
		echo "<tr><td><b>${group1[${i}]}</b></td><td><b>$usergroup</b></td>"
		echo "<td><a href='user_groupadd.php?group=${group1[${i}]}'>
			<input type='submit' class='table_button' value='管理用戶'></a></td>"

		if [ "$usergroup" == "" ]; then
			echo "<td><a href='?delgroup=yes&groupname=${group1[${i}]}'>
				<input type='submit' class='table_button' value='刪除' 
				OnClick='return checkdelgroup()'></a></td>"
		else
			echo "<td>刪除</td>"
		fi
		echo "</tr>"
	fi
done

