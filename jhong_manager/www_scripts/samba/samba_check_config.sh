#!/bin/bash

# 1. 檢查一下，如果沒有正確的 /etc/samba/jhong ，就建立他!
	[ ! -d /etc/samba/jhong ] && mkdir /etc/samba/jhong

# 2. 說明，原始檔案為 /etc/samba/smb.conf ，而暫時處理的檔案為 /dev/shm/jhong/samba_config_file_check
	rawfile=/etc/samba/smb.conf
	newfile=/dev/shm/jhong/samba_config_file_check
	[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong
	cp $rawfile $newfile

# 2. 查出檔案的 server string = 所在行
	adding=$( grep -n '^[[:space:]]*server string' $newfile | cut -d ':' -f 1)

# 3. 如果沒有  netbios name 就給他加進去
	testing=$(grep '^[[:space:]]*netbios name' $newfile)
	if [ "$testing" == "" ]; then
		sed -i "${adding} a\
	\\\tnetbios name = Jhong-server" $newfile
		addnew="yes"
	fi

# 4. 檢測一下，給予編碼設定
	testing=$(grep '^[[:space:]]*dos charset' $newfile)
	if [ "$testing" == "" ]; then
		sed -i "${adding} a\
	\\\tdos charset = CP950" $newfile
		sed -i "${adding} a\
	\\\tunix charset = UTF-8" $newfile
		addnew="yes"
	fi

# 5. 所以，修訂檔案了
	testing=$( diff $rawfile $newfile )
	if [ "$testing" != "" ]; then
		cp $newfile $rawfile
	fi
