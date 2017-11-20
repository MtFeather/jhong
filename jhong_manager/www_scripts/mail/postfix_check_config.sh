#!/bin/bash

# 1. 先處理一下將設定檔移動到 /dev/shm 去！
	rawfile=/etc/postfix/main.cf
	newfile=/dev/shm/jhong/postfix_config_check.cf
	[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong
	cp $rawfile $newfile

# 2. 檢查一下 inet_interfaces 的設定值
	testing=$( grep '^inet_interfaces = localhost' $newfile )
	if [ "$testing" != "" ]; then
		sed -i 's/^inet_interfaces =.*$/inet_interfaces = all/g' $newfile
	fi

# 3. 檢查一下有沒有 myhostname 這個設定？若沒有，就加上去！
	testing=$( grep '^myhostname =' $newfile )
	if [ "$testing" == "" ]; then
		echo "myhostname = localhost" >> $newfile
	fi

# 4. 檢查一下有沒有 myorigin 設定是否正確為 myhostname
	testing=$( grep '^#myorigin = $myhostname' $newfile )
	if [ "$testing" != "" ]; then
		sed -i 's/^#myorigin = $myhostname.*$/myorigin = $myhostname/g' $newfile
	fi

# 5. 檢查一下有沒有 mynetworks 這個設定？若沒有，就加上去！
	testing=$( grep '^mynetworks =' $newfile )
	if [ "$testing" == "" ]; then
		echo "mynetworks = 127.0.0.0/8" >> $newfile
	fi

# 5. 檢查一下有沒有 mailbox_size_limit 這個設定？若沒有，就加上去！
	testing=$( grep '^mailbox_size_limit =' $newfile )
	if [ "$testing" == "" ]; then
		echo "mailbox_size_limit = 51200000" >> $newfile
	fi

# 5. 檢查一下有沒有 這個設定？若沒有，就加上去！
	testing=$( grep '^message_size_limit =' $newfile )
	if [ "$testing" == "" ]; then
		echo "message_size_limit = 10240000" >> $newfile
	fi

# 5. 檢查一下有沒有  這個設定？若沒有，就加上去！
	testing=$( grep '^smtpd_recipient_limit =' $newfile )
	if [ "$testing" == "" ]; then
		echo "smtpd_recipient_limit = 1000" >> $newfile
	fi

# 5. 檢查一下有沒有  這個設定？若沒有，就加上去！
	testing=$( grep '^default_destination_recipient_limit =' $newfile )
	if [ "$testing" == "" ]; then
		echo "default_destination_recipient_limit = 50" >> $newfile
	fi

# 5. 檢查一下有沒有  這個設定？若沒有，就加上去！
	testing=$( grep '^virtual_alias_maps ' $newfile )
	if [ "$testing" == "" ]; then
		echo "virtual_alias_maps = hash:/etc/postfix/virtual" >> $newfile
	fi


# 6. 所以，修訂檔案了
        testing=$( diff $rawfile $newfile )
        if [ "$testing" != "" ]; then
                cp $newfile $rawfile
		systemctl restart MailScanner
        fi
