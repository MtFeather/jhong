#!/bin/bash

basedir=/dev/shm/jhong/samba_dir/
[ ! -d $basedir ] && mkdir $basedir
touch $basedir/.samba_filesystem
cd /jhong/samba
filenames=$( ls -d * )
if [ "$filenames" != "" ]; then
	for filename in $filenames
	do
		getfacl $filename > $basedir/$filename
	done
	du -sm /jhong/samba/* | awk '{print $2, $1}' > $basedir/.samba_filesystem

fi
