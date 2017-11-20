<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$my_user_ftpchroot[$i]		// 有沒有被 chroot 呢
	$my_user_home_du[$i]		// 使用掉多少容量呢

*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");

	/*echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	// 先檢查看看基本設定有沒有問題
	$log = shell_exec ( "sudo sh $sdir/ftp/ftp_check_config.sh 2>&1" );

	// 先建立所需要的各項帳號資訊喔！
	$log = exec ( "
		sudo bash -c \"
		[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong;
		du -sm /home/* | awk '{print \\\$1, \\\$2}' | grep -v '/mail_' > /dev/shm/jhong/userinfo.ftp.du
		cat /etc/vsftpd/chroot_list | sort > /dev/shm/jhong/userinfo.ftp.nochroot
		\" 2>&1 " );

	// 讀進帳號資訊了
	include ( "../account/function.php" );

	// 取得每個家目錄的容量
	$ftpdufile = fopen ( "/dev/shm/jhong/userinfo.ftp.du", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.ftp.du\" file");
	$ftpdunumber = 0;
	while ( ! feof($ftpdufile) ) {
		$temp_userinfo = fgets($ftpdufile);
		$userinfo_line = explode ( " ", $temp_userinfo );
		$ftpdunumber = $ftpdunumber + 1;
		$ftpdir[$ftpdunumber] = trim($userinfo_line[1]);
		$ftpdu[$ftpdunumber]  = trim($userinfo_line[0]);
	}
	/*echo "$ftpdunumber <br />";
	for ( $i=1; $i<=$ftpdunumber; $i++ ) {
		echo "'$ftpdir[$i]' ==> '$ftpdu[$i]' <br />";
	}*/
	for ( $i=1; $i<=$usernumber; $i++ ) {
		$my_user_home_du[$i] = 0;
		for ( $j=1; $j<=$ftpdunumber; $j++ ) {
			if ( $my_user_home[$i] == $ftpdir[$j] ) $my_user_home_du[$i] = $ftpdu[$j]; 
		}
		//echo "$my_username[$i] ==> $my_user_home_du[$i] <br />";
	}

	// 取得沒有 chroot 的帳號名稱
	$ftpnochrootfile = fopen ( "/dev/shm/jhong/userinfo.ftp.nochroot", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.ftp.nochroot\" file");
	$ftpnochrootnumber = 0;
	while ( ! feof($ftpnochrootfile) ) {
		$temp_userinfo = fgets($ftpnochrootfile);
		$ftpnochrootnumber = $ftpnochrootnumber + 1;
		$ftpnochroot[$ftpnochrootnumber]  = trim($temp_userinfo);
	}
	/*echo "$ftpdunumber <br />";
	for ( $i=1; $i<=$ftpdunumber; $i++ ) {
		echo "'$ftpdir[$i]' ==> '$ftpdu[$i]' <br />";
	}*/
	for ( $i=1; $i<=$usernumber; $i++ ) {
		$my_user_ftpchroot[$i] = "yes";
		foreach ( $ftpnochroot as $temp ) {
			if ( $my_username[$i] == $temp ) $my_user_ftpchroot[$i] = "no"; 
		}
		//echo "$my_username[$i] ==> $my_user_ftpchroot[$i] <br />";
	}

	/*echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

?>

