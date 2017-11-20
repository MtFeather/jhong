<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$smbnumber				// Samba 分享的資源總數量
	$smb_filename[$smbnumber]		// 每個獨立的設定檔所在絕對路徑
	$smb_sharename[$smbnumber]		// 分享的名稱
	$smb_dirname[$smbnumber]		// 所在目錄絕對路徑
	$smb_writable[$smbnumber]		// yes/no
	$smb_perm_group[$smbnumber][$gnu]	// 可操作此目錄的群組
	$smb_perm_user[$smbnumber][$gnu]	// 可操作此目錄的用戶
	$smb_perm_groupro[$smbnumber][$gnuro]	// 唯讀的群組
	$smb_du[$smbnumber]			// 檔案容量

*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");

	/*echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

	// 先檢查看看基本設定有沒有問題
	$log = shell_exec ( "sudo sh $sdir/samba/samba_check_config.sh 2>&1" );

	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	// 先建立所需要的各項帳號資訊喔！
	$log = exec ( "
		sudo bash -c \"
			sh $sdir/samba/samba_dir.sh;
			[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong; 
			cat /etc/samba/smb.conf | grep -v '^[[:space:]]*#' |grep -v '[[:space:]]*;' | col -x |\
				grep -v '^$' | sort  > /dev/shm/jhong/samba_config_file_raw
	\" " );

	// 先取得原始的系統設定內容
	$tempfs_file = fopen ( "/dev/shm/jhong/samba_dir/.samba_filesystem", "r" ) or die ("Can't open \"/dev/shm/jhong/samba_dir/.samba_filesystem\" file");
	$tempfs_nu = 0;
	while ( ! feof($tempfs_file) ) {
		$tempfs_nu = $tempfs_nu + 1;
		$tempfs_line = fgets($tempfs_file);
		$tempfs_row = explode ( " ", $tempfs_line );
		$smbfs_name[$tempfs_nu] = $tempfs_row[0];
		$smbfs_size[$tempfs_nu] = trim(str_replace("\r\n","",$tempfs_row[1]));
	}

	$smb_server = fopen ( "/dev/shm/jhong/samba_config_file_raw", "r" ) or die ("Can't open \"/dev/shm/jhong/samba_config_file_raw\" file");
	$i = 0;
	$smbnumber = 0;
	while ( ! feof($smb_server) ) {
		$temp_info = fgets($smb_server);
		$temp_line = explode ( "=", $temp_info );
		if ( trim($temp_line[0]) == "workgroup" )     $smb_server_workgroup    = trim($temp_line[1]);
		if ( trim($temp_line[0]) == "server string" ) $smb_server_serverstring = trim($temp_line[1]);
		if ( trim($temp_line[0]) == "dos charset" )   $smb_server_doscharset   = trim($temp_line[1]);
		if ( trim($temp_line[0]) == "unix charset" )  $smb_server_unixcharset  = trim($temp_line[1]);
		if ( trim($temp_line[0]) == "netbios name" )  $smb_server_netbiosname  = trim($temp_line[1]);
		if ( trim($temp_line[0]) == "include" ) {
			$smbnumber = $smbnumber + 1;
			$smb_filename[$smbnumber]  = trim($temp_line[1]);
			$thistemp                  = explode ( "/", $smb_filename[$smbnumber]);
			$smb_sharename[$smbnumber] = $thistemp[4];
			$smb_dirname[$smbnumber]   = "/jhong/samba/$smb_sharename[$smbnumber]";
			$setfile = fopen ( "$smb_filename[$smbnumber]", "r" ) or die ("Can't open \"$smb_filename[$smbnumber]\" file");
			while ( ! feof($setfile) ) {
				$temp_line2 = fgets($setfile);
				$temp_line2 = explode ( "=", $temp_line2 );
				if ( trim($temp_line2[0]) == "writable" )     $smb_writable[$smbnumber] = trim($temp_line2[1]);
				if ( trim($temp_line2[0]) == "valid users" ) {
					$thistemp2 = trim($temp_line2[1]);
					$temp_line3 = explode ( ",", $thistemp2 );
					$gnu = 0; $unu = 0; $gnuro = 0;
					foreach ( $temp_line3 as $temp ) {
						$temp = trim($temp);
						if ( substr($temp,0,1) == "@" ) {
							// 取得的就是群組名稱
							$temp_groupname = str_replace( "@", "", $temp );
							$temp_filename = "/dev/shm/jhong/samba_dir/$smb_sharename[$smbnumber]";
							$temp_aclfile = fopen ( "$temp_filename", "r" ) or die ("Can't open \"$temp_filename\" file");
							while ( ! feof($temp_aclfile) ) {
								$temp2_line = fgets($temp_aclfile);
								$temp2_row  = explode (":",$temp2_line);
								if ( $temp2_row[0] == "group" && $temp2_row[1] == "$temp_groupname" ) {
									if ( substr($temp2_row[2],0,3) == "rwx" ) {
										$gnu = $gnu + 1;
										$smb_perm_group[$smbnumber][$gnu] = $temp_groupname;

									} else {
										$gnuro = $gnuro + 1;
										$smb_perm_groupro[$smbnumber][$gnuro] = $temp_groupname;
									}
								}

							}
							fclose ( $temp_aclfile );
						} else {
							// 取得的就是單獨帳號名稱
							$unu = $unu + 1;
							$smb_perm_user[$smbnumber][$unu] = $temp;
						}
					}
				}
			}
			$smb_du[$smbnumber] = 0;
			for ( $j=1; $j<=$tempfs_nu; $j++) {
				if ( $smb_dirname[$smbnumber] == $smbfs_name[$j] ) {
					$smb_du[$smbnumber] = $smbfs_size[$j];
					break;
				}
			}
		}
		$i++;
		if ( $i > 500 ) die;
	}

	/*for ( $i=1; $i<=$smbnumber; $i++ ) {
		echo "$smb_sharename[$i]|$smb_dirname[$i]|$smb_writable[$i]|group=";
		foreach ( $smb_perm_group[$i] as $temp ) echo "${temp},";
		echo "|user=";
		foreach ( $smb_perm_user[$i]  as $temp ) echo "${temp},";
		echo "<br />";
	}
	echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

?>

