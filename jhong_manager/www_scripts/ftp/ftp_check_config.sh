#!/bin/bash

ftpfile="/etc/vsftpd/vsftpd.conf"
# 1. 確認 chroot 的設定是否存在！若不存在，就加入正確的 chroot 
	testing=$( grep '^chroot' $ftpfile )
	if [ "$testing" != "" ]; then
		echo "Chroot had setup OK"
		exit
	fi
echo "
chroot_local_user=YES
chroot_list_enable=YES
chroot_list_file=/etc/vsftpd/chroot_list
allow_writeable_chroot=YES" >> $ftpfile

	[ ! -f /etc/vsftpd/chroot_list ] && touch /etc/vsftpd/chroot_list

	systemctl restart vsftpd
	systemctl enable vsftpd

