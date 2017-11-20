#!/bin/bash

wwwfile="/etc/httpd/conf/httpd.conf"

# 1. 確認各項設定值是否存在？若不存在就主動加入，不然就停止
	for iteam in Listen ServerName ServerAdmin DocumentRoot
	do
		testing=$( grep "^${iteam}" $wwwfile )
		if [ "$testing" == "" ]; then
			sed -i "s/#${iteam}/${iteam}/g" $wwwfile
		fi
	done

	testing=$( grep '^AddDefaultCharset' $wwwfile )
	if [ "$testing" != "" ]; then
		sed -i 's/AddDefaultCharset/#AddDefaultCharset/g' $wwwfile
	fi

	testing=$( grep 'jhongadmin' $wwwfile )
	if [ "$testing" == "" ]; then
		echo "#jhongadmin root" >> $wwwfile
	fi

# 2. 檢查 rc.local 是否有加入掛載的全參數？
	testing=$( grep '/jhong/bin/wwwmount.sh' /etc/rc.d/rc.local )
	if [ "$testing" == "" ]; then
		echo "sh /jhong/bin/wwwmount.sh all" >> /etc/rc.d/rc.local
		chmod a+x /etc/rc.d/rc.local
	fi

	[ ! -d /jhong/www ] && mkdir /jhong/www
