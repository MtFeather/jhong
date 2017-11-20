<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$www_listen			// 監聽的 port
	$www_servername			// 伺服器的名稱
	$www_serveradmin		// 伺服器的管理員 email 
	$www_documentroot		// 文件根目錄
	$www_options			// 文件根目錄的許多操作特性
	$www_allowoverride 		// 是否允許複寫許多資料

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
	$log = shell_exec ( "sudo sh $sdir/www/www_check_config.sh 2>&1" );

	// 先建立 apache 的各項資訊給他抓出來
	$log = exec ( "
		sudo bash -c \"
		[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong;
		grep -v '^[[:space:]]*#' /etc/httpd/conf/httpd.conf  | grep -v '^$' > /dev/shm/jhong/apache_config;
		\" 2>&1 " );

	// 取得相關資訊
	$wwwfile = fopen ( "/dev/shm/jhong/apache_config", "r" ) or die ("Can't open \"/dev/shm/jhong/apache_config\" file");
	while ( ! feof($wwwfile) ) {
		$temp_info = fgets($wwwfile);
		$temp_info = preg_replace( "/^[[:space:]]*/", "", $temp_info );
		$info_line = explode ( " ", $temp_info );
		if ( count( $info_line ) > 1 ) {
			if ( trim($info_line[0]) == "Listen" )       { $www_listen       = trim($info_line[1]) ; continue; }
			if ( trim($info_line[0]) == "ServerName" )   { $www_servername   = trim($info_line[1]) ; continue; }
			if ( trim($info_line[0]) == "ServerAdmin" )  { $www_serveradmin  = trim($info_line[1]) ; continue; }
			if ( trim($info_line[0]) == "DocumentRoot" ) { 
				$www_documentroot = str_replace('"',"",trim($info_line[1])) ;
				continue; 
			}
			if ( trim($info_line[0]) == "Options" )      { 
				if ( ! isset ( $www_options ) ) {
					for ( $i=1; $i<=count($info_line); $i++ ) {
						$www_options[$i] = trim($info_line[$i]) ;
						continue;
					}
				}
			}
			if ( trim($info_line[0]) == "AllowOverride" ) { 
				if ( isset ( $www_options ) && ! isset ( $www_allowoverride ) ) {
					$www_allowoverride = trim($info_line[1]) ;
				}
			}
		}
	}

	$www_jhongadmin = shell_exec ( "grep 'jhongadmin' /etc/httpd/conf/httpd.conf | cut -d ' ' -f 2" );
	$www_jhongadmin = trim($www_jhongadmin);

	/*echo "$www_listen|$www_servername|$www_serveradmin|$www_documentroot|options=";
	foreach ( $www_options as $temp ) echo $temp;
	echo "|$www_allowoverride <br />";*/

	/*echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

	$filetemp = exec ( "echo \$(ls /etc/httpd/conf.d/jhong_[a-zA-Z]*.conf)" );
	$files = explode ( " ", $filetemp );
	$wwwvnumber = 0;
	foreach ( $files as $tempme ) {
		if ( $tempme != "" ) {
			$wwwvnumber = $wwwvnumber + 1;
			$wwwvfile = fopen ( $tempme, "r" ) or die ("Can't open \"$tempme\" file");
			while ( ! feof($wwwvfile) ) {
				$temp_info = fgets($wwwvfile);
				$temp_info = preg_replace( "/^[[:space:]]*/", "", $temp_info );
				$info_line = explode ( " ", $temp_info );
				if ( count ($info_line) >= 2 ) {
					if ( trim($info_line[0]) == "ServerName" ) $www_v_servername[$wwwvnumber] = trim($info_line[1]);
					if ( trim($info_line[0]) == "ServerAlias" ) {
						$www_v_serveralias[$wwwvnumber] = "";
						for ( $i=1; $i<=count($info_line); $i++ ) {
						    if ( trim($info_line[$i]) != "" ) {
							if ( $www_v_serveralias[$wwwvnumber] == "" ) 
								$www_v_serveralias[$wwwvnumber] = trim($info_line[$i]) ;
							else
								$www_v_serveralias[$wwwvnumber] = $www_v_serveralias[$wwwvnumber] . " " . trim($info_line[$i]) ;
						    }
						}
					}
					if ( trim($info_line[0]) == "ServerAdmin" ) $www_v_serveradmin[$wwwvnumber] = trim($info_line[1]);
					if ( trim($info_line[0]) == "DocumentRoot" ) $www_v_documentroot[$wwwvnumber] = trim($info_line[1]);
					if ( trim($info_line[0]) == "AllowOverride" ) $www_v_allowoverride[$wwwvnumber] = trim($info_line[1]);
					if ( trim($info_line[0]) == "#jhongadmin" ) $www_v_jhongadmin[$wwwvnumber] = trim($info_line[1]);
					if ( trim($info_line[0]) == "Options" )      { 
						for ( $i=1; $i<=count($info_line); $i++ ) {
							if ( trim($info_line[$i]) != "" ) 
								$www_v_options[$wwwvnumber][$i] = trim($info_line[$i]) ;
						}
					}
				}
			}
			fclose ( $wwwvfile );
		}
	}

	/*echo $wwwvnumber;
	for ( $i=1;$i<=$wwwvnumber; $i++ ) {
		echo "$www_v_servername[$i]|$www_v_serveralias[$i]|$www_v_serveradmin[$i]|$www_v_documentroot[$i]|$www_v_allowoverride[$i]|$www_v_jhongadmin[$i]|options=<br />";
		foreach ( $www_v_options[$i] as $tempqq ) { echo $tempqq . ","; }
		echo "<br /><br />";
	} */

?>

