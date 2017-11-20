#!/bin/bash

# 本檔案的系統設定檔！重要！
filename="/jhong/bin/wwwmount.txt"

if [ "${1}" == "" ]; then
	exit 
fi

function mymount() {
	mydir=${1}
	myuserdir=${2}
	myusername=${3}
	for checking in $mydir $myuserdir $myusername
	do
		if [ "$checking" == "" ]; then
			exit 
		fi
	done
	myuserdir="/home/${myusername}/www_${myuserdir}"
	[ ! -d $myuserdir ] && mkdir $myuserdir
	umount $myuserdir
	mount --bind $mydir $myuserdir
	setfacl -b $myuserdir
	setfacl -R -m   u:$myusername:rwx $myuserdir
	setfacl -R -m d:u:$myusername:rwx $myuserdir
}

# 從設定檔當中撈出系統，全部都掛載的狀態
if [ "${1}" == "all" ]; then
	[ ! -f $filename ] && touch $filename

	total=$( wc -l $filename | cut -d ' ' -f 1)
	if [ "$total" == "0" ]; then
		exit 
	fi

	for i in $( seq 1 $total )
	do
		linetemp=$(sed -n "${i}p" $filename)
		mydir=$( echo $linetemp | cut -d ' ' -f 1)
		myuserdir=$( echo $linetemp | cut -d ' ' -f 2)
		myusername=$( echo $linetemp | cut -d ' ' -f 3)
		mymount $mydir $myuserdir $myusername
	done
fi

if [ "${1}" == "mount" ]; then
	mydir=${2}
	myuserdir=${3}
	myusername=${4}
	echo "$mydir $myuserdir $myusername" >> $filename
	mymount $mydir $myuserdir $myusername
fi

if [ "${1}" == "umount" ]; then
	mydir=${2}
	myuserdir=${3}
	myusername=${4}
	myline=$(grep -n "$mydir $myuserdir $myusername" $filename | cut -d ':' -f 1)
	sed -i "${myline}d" $filename
	mountdir="/home/${myusername}/www_${myuserdir}"
	if [ -d "$mountdir" ]; then
		umount $mountdir
		rmdir $mountdir
	fi
fi
