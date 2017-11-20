#!/bin/bash

backupdir="/jhong/backup"
[ ! -d $backupdir ] && mkdir $backupdir
if [ "${1}" == "" ]; then
	exit 0
fi

option=${1}

if [ $option == "check" ]; then
	testing=$( ps -e -o cmd | grep 'cp ' | grep $backupdir )
	if [ "$testing" != "" ]; then
		echo "copying"
	else
		echo "OK"
	fi
fi

if [ $option == "disk" ]; then
	lsblk -lp -o NAME,SIZE,TYPE,VENDOR,RM,MODEL | egrep '^NAME|^/dev/... '
	exit 0
fi

if [ $option == "partition" ]; then
	disk=${2}
	lsblk -lp -o NAME,FSTYPE,SIZE,MOUNTPOINT,TYPE,VENDOR,RM,MODEL $disk | egrep '^NAME|^/dev/...[0-9] '
	exit 0
fi

if [ $option == "copy" ]; then
	partition=${2}
	shift 2
	source="$@"

	if [ "$source" == "" ]; then
		echo "沒有任何要備份的資料，就此打住！"
		exit 0
	fi

	# 1. 確認沒有掛載中
	testing=$(lsblk -n $partition -o MOUNTPOINT)
	if [ "$testing" != "" ]; then
		echo "此檔案系統正在掛載使用中，不可重複掛載！"
		exit
	fi

	# 2. 確認掛載點沒有被使用中
	for i in $(seq 1 5)
	do
		testing=$(df | grep "${backupdir}$")
		if [ "$testing" != "" ]; then
			umount $backupdir
		else
			status="OK"
			break
		fi
	done

	if [ "$status" != "OK" ]; then
		echo "掛載目錄出問題，很可能目前有其他工作正在進行，請稍後重試"
		exit
	fi

	# 3. 開始進行掛載
	mount $partition $backupdir ; res=$?
	if [ "$?" != 0 ]; then
		echo "無法掛載！可能是選擇的分割槽所在的檔案系統不被本系統所支援，請更換另一個裝置並重新測試！"
		exit 0
	fi

	# 4. 開始進行複製行為
	[ ! -d $backupdir/jhong_backup_dir ] && mkdir $backupdir/jhong_backup_dir
	cp -a $source $backupdir/jhong_backup_dir
	sync; sync
	umount $backupdir
	umount -l $backupdir &> /dev/null
fi

