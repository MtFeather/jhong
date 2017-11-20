#!/bin/bash

listen=${1}
servername=${2}
serveradmin=${3}
documentroot=${4}
options=${5}
allowoverride=${6}
old_doc=${7}
documentroot=$( echo $documentroot | sed 's/\//\\\//g' )
options=$( echo $options | sed 's/_/ /g' )
old_doc=$( echo $old_doc | sed 's/\//\\\//g' )

wwwfile=/dev/shm/jhong/httpd.conf.config
cp /etc/httpd/conf/httpd.conf $wwwfile

# 1. 先取代一些基礎設定
sed -i "s/^Listen.*$/Listen $listen/g" $wwwfile
sed -i "s/^ServerName.*$/ServerName $servername/g" $wwwfile
sed -i "s/^ServerAdmin.*$/ServerAdmin $serveradmin/g" $wwwfile
sed -i "s/^DocumentRoot.*$/DocumentRoot \"$documentroot\"/g" $wwwfile
sed -i "s/^<Directory \"$old_doc\">/<Directory \"$documentroot\">/g" $wwwfile

# 2. 找到目錄設定的開始位置
line=$(grep -n "<Directory \"${documentroot}\"" $wwwfile| cut -d ':' -f 1)

# 3. 找到正確的 Optinos 設定行號
linetemp=$(grep -n '^[[:space:]]*Options' $wwwfile | cut -d ':' -f 1)
for i in ${linetemp}
do 
	if [ $i -gt $line ]; then
		lineoptions=$i
		sed -i "${lineoptions}d" $wwwfile
		sed -i "${lineoptions}i \ \ \ \ Options ${options}" $wwwfile
		break
	fi
done 

# 4. 找到正確的 AllowOverride 設定行號
linetemp=$(grep -n '^[[:space:]]*AllowOverride' $wwwfile | cut -d ':' -f 1)
for i in ${linetemp}
do 
	if [ $i -gt $line ]; then
		lineover=$i
		sed -i "${lineover}d" $wwwfile
		sed -i "${lineover}i \ \ \ \ AllowOverride $allowoverride" $wwwfile
		break
	fi
done 

# 5. 將資料複製回來囉
cp $wwwfile /etc/httpd/conf/httpd.conf
