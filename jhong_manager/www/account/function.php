<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$usernumber			// 帳號總數量
	$my_username[$usernumber] 	// 最原始的帳號名稱
	$my_user_uid[$usernumber] 	// 使用者的 UID
	$my_user_groupid[$usernumber] 	// 使用者的初始 GID
	$my_user_groupname[$usernumber]	// 這個初始 GID 的群組名稱
	$my_user_gecos[$usernumber] 	// 使用者的說明內容
	$my_user_home[$usernumber]	// 使用者的家目錄所在
	$my_user_shell[$usernumber]	// 使用者用的 shell 喔！
	$my_user_bk[$usernumber] 	// 帳號是否為凍結狀態
	$my_user_mail[$usernumber]  	// 是否具有郵件帳號的備份資料
	$my_user_smb[$usernumber]  	// 是否具 Samba 支援的帳號

	$groupnumber				// 群組的總數
	$my_groupname[$groupnumber]		// 群組名稱
	$my_groupgid[$groupnumber] 		// 群組的 GID ！
	$my_group_users[$groupnumber][$i] 	// 加入此群組的用戶群，僅有次要群組喔！
*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");
	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	//echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	//echo "<br />";

	// 先建立所需要的各項帳號資訊喔！
	$log = exec ( "
		sudo bash -c \"
			[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong; 
			cat /etc/passwd | sed 's/^bk_//g' | grep -v '^mail_' | sort > /dev/shm/jhong/userinfo.raw
			cat /etc/passwd | grep '^bk_'   | sed 's/^bk_//g'   | sort > /dev/shm/jhong/userinfo.backup
			cat /etc/passwd | grep '^mail_' | sed 's/^mail_//g' | sort > /dev/shm/jhong/userinfo.mail
			cat /etc/group | grep -v '^mail_' | sort > /dev/shm/jhong/group.raw
			pdbedit -L | sed 's/^bk_//g' | sort > /dev/shm/jhong/userinfo.samba
	\" " );

	// 取得被凍結的帳號名稱
	$userfilebk = fopen ( "/dev/shm/jhong/userinfo.backup", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.backup\" file");
	$bknumber = 0;
	while ( ! feof($userfilebk) ) {
		$temp_userinfo = fgets($userfilebk);
		$userinfo_line = explode ( ":", $temp_userinfo );
		$bknumber = $bknumber + 1;
		$bknames[$bknumber] = $userinfo_line[0];
	}
	/*foreach ( $bknames as $bkname ) {
		echo $bkname . "<br />";
	}*/

	// 取得有郵件備份的帳號
	$userfilemail = fopen ( "/dev/shm/jhong/userinfo.mail", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.mail\" file");
	$mailnumber = 0;
	while ( ! feof($userfilemail) ) {
		$temp_userinfo = fgets($userfilemail);
		$userinfo_line = explode ( ":", $temp_userinfo );
		$mailnumber = $mailnumber + 1;
		$mailnames[$mailnumber] = $userinfo_line[0];
	}
	/* foreach ( $mailnames as $mailname ) {
		echo $mailname . "<br />";
	} */

	// 取得有 Samba 支援的帳號
	$userfilesmb = fopen ( "/dev/shm/jhong/userinfo.samba", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.samba\" file");
	$smbusernumber = 0;
	while ( ! feof($userfilesmb) ) {
		$temp_userinfo = fgets($userfilesmb);
		$userinfo_line = explode ( ":", $temp_userinfo );
		$smbusernumber = $smbusernumber + 1;
		$smbnames[$smbusernumber] = $userinfo_line[0];
	}
	/*foreach ( $smbnames as $smbname ) {
		echo $smbname . "<br />";
	}*/

	// 取得群組相關的資料
	$groupfileraw = fopen ( "/dev/shm/jhong/group.raw", "r" ) or die ("Can't open \"/dev/shm/jhong/group.raw\" file");
	$groupnumber = 0;
	while ( ! feof($groupfileraw) ) {
		$temp_userinfo = fgets($groupfileraw);
		$userinfo_line = explode ( ":", $temp_userinfo );
		if ( $userinfo_line[2] > 999 and $userinfo_line[2] < 60001 ) {
			$groupnumber = $groupnumber + 1;
			$my_groupname[$groupnumber] = $userinfo_line[0];
			$my_groupgid[$groupnumber] = $userinfo_line[2];
			$userinfo_line[3] = str_replace("\n","",$userinfo_line[3]);		// 去除斷行
			if ( $userinfo_line[3] != "" ) {
				$my_group_users_all = explode (",", $userinfo_line[3]);
				$i = 0;
				foreach ( $my_group_users_all as $grouptemp ) {
					$i = $i + 1;
					$my_group_users[$groupnumber][$i] = $grouptemp;
				}
			}
		}
	}
	/* for ( $i=1; $i<=$groupnumber; $i++ ) {
		echo "groupname: $my_groupname[$i], groupgid: $my_groupgid[$i], groups: "; 
		if ( count ( $my_group_users[$i] ) != 0 ) {
			foreach ( $my_group_users[$i] as $grouptemp ) {
				echo $grouptemp;
			}
		}
		echo "<br />";
	}  */

	// 取得大於 1000 小於 60000 以下的一般帳號資訊！
	$userfileraw = fopen ( "/dev/shm/jhong/userinfo.raw", "r" ) or die ("Can't open \"/dev/shm/jhong/userinfo.raw\" file");
	$usernumber = 0;
	while ( ! feof($userfileraw) ) {
		$temp_userinfo = fgets($userfileraw);
		$userinfo_line = explode ( ":", $temp_userinfo );
		$usertemp1 = $userinfo_line[0];
		$usertemp3 = $userinfo_line[2];
		$usertemp4 = $userinfo_line[3];
		$usertemp5 = $userinfo_line[4];
		$usertemp6 = $userinfo_line[5];
		$usertemp7 = str_replace("\n","",$userinfo_line[6]);
		if ( $usertemp3 > 999 and $usertemp3 < 60001 ) {
			$usernumber = $usernumber + 1;			// 帳號總數量
			$my_username[$usernumber]     = $usertemp1;	// 最原始的帳號名稱
			$my_user_uid[$usernumber]     = $usertemp3;	// 使用者的 UID
			$my_user_groupid[$usernumber] = $usertemp4;	// 使用者的初始 GID
			$my_user_gecos[$usernumber]   = $usertemp5;	// 使用者的說明內容
			$my_user_home[$usernumber]    = $usertemp6;	// 使用者的家目錄所在
			$my_user_shell[$usernumber]   = $usertemp7;	// 使用者用的 shell 喔！
			// 判斷是否有凍結
			$my_user_bk[$usernumber] = "no";		// 帳號是否為凍結狀態
			foreach ( $bknames as $bkname ) {
				if ( $bkname == $my_username[$usernumber] ) {
					$my_user_bk[$usernumber] = "yes";
					break;
				}
			}
			// 帳號是否有郵件備份的帳號存在？
			$my_user_mail[$usernumber] = "no";		// 是否具有郵件帳號的備份資料
			foreach ( $mailnames as $mailname ) {
				if ( $mailname == $my_username[$usernumber] ) {
					$my_user_mail[$usernumber] = "yes";
					break;
				}
			}
			// 帳號是否有郵件備份的帳號存在？
			$my_user_smb[$usernumber] = "no";		// 是否具有 Samba 支援
			foreach ( $smbnames as $smbname ) {
				if ( $smbname == $my_username[$usernumber] ) {
					$my_user_smb[$usernumber] = "yes";
					break;
				}
			}
			// 追蹤一下初始群組名稱
			for ( $i=1; $i<=$groupnumber; $i++ ) {
				if ( $my_groupgid[$i] == $my_user_groupid[$usernumber] ) {
					$my_user_groupname[$usernumber] = $my_groupname[$i];		// 成為主要群組
					if ( count ( $my_group_users[$i] ) != 0 ) {
						for ( $j = 1; $j <= count ( $my_group_users[$i] ); $j++ ) {
							if ( $my_group_users[$i][$j] == $my_username[$usernumber] ) {
								unset ($my_group_users[$i][$j]) ;	// 若在次要群組內，請取消設定！
							}
						}
					}
				}
			}

		}
	}

	// 將結果給她輸出一下！你可以取消來查閱相關的輸出結果！
	/*for ( $i=1; $i<=$usernumber; $i++ ) {
		echo "username: $my_username[$i], UID: $my_user_uid[$i], GID: $my_user_groupid[$i], GIDgroupname: $my_user_groupname[$i], myname: $my_user_gecos[$i], home: $my_user_home[$i], shell: $my_user_shell[$i], backup: $my_user_bk[$i], mail account: $my_user_mail[$i] <br /> "; 
	} 
	echo "<br /><br />";
	for ( $i=1; $i<=$groupnumber; $i++ ) {
		echo "groupname: $my_groupname[$i], groupgid: $my_groupgid[$i], groups: "; 
		if ( count ( $my_group_users[$i] ) != 0 ) {
			foreach ( $my_group_users[$i] as $grouptemp ) {
				echo $grouptemp;
			}
		}
		echo "<br />";
	} */

	//echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
?>

