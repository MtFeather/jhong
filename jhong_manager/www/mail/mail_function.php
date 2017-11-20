<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	"myhostname"               		$mail_hostname
	"mydestination"            		$mail_dest
	"mynetworks"               		$mail_clients
	"mailbox_size_limit"       		$mail_mbox_size
	"message_size_limit"       		$mail_mesg_size 
	"smtpd_recipient_limit"    		$mail_smtpd
	"default_destination_recipient_limit"	$mail_recipient

*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");

	//echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	//echo "<br />";

	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	// 先檢查看看基本設定有沒有問題
	$log = shell_exec ( "sudo sh $sdir/mail/postfix_check_config.sh 2>&1" );

	// 先建立所需要的各項帳號資訊喔！
	$log = exec ( "
		sudo bash -c \"
			[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong; 
			cat /etc/postfix/main.cf | grep -v '^[[:space:]]*#' | grep -v '^[[:space:]]*$' > /dev/shm/jhong/postfix_config_file_raw
	\" " );

	// 先取得原始的系統設定內容
	$mail_server = fopen ( "/dev/shm/jhong/postfix_config_file_raw", "r" ) or die ("Can't open \"/dev/shm/jhong/postfix_config_file_raw\" file");
	$i = 0;
	while ( ! feof($mail_server) ) {
		$temp_info = fgets($mail_server);
		$temp_info = str_replace(" ","",$temp_info);
		$temp_info = str_replace("\r\n","",$temp_info);
		$temp_line = explode ( "=", $temp_info );
		if ( trim($temp_line[0]) == "myhostname" )		$mail_hostname 	= trim($temp_line[1]);
		if ( trim($temp_line[0]) == "mydestination" ) 		$mail_dest	= explode (",",trim($temp_line[1]));
		if ( trim($temp_line[0]) == "mynetworks" ) 		$mail_clients	= explode (",",trim($temp_line[1]));
		if ( trim($temp_line[0]) == "mailbox_size_limit" )	$mail_mbox_size	= trim($temp_line[1]);
		if ( trim($temp_line[0]) == "message_size_limit" )	$mail_mesg_size	= trim($temp_line[1]);
		if ( trim($temp_line[0]) == "smtpd_recipient_limit" )	$mail_smtpd 	= trim($temp_line[1]);
		if ( trim($temp_line[0]) == "default_destination_recipient_limit" )	$mail_recipient	= trim($temp_line[1]);

		$i++;
		if ( $i > 500 ) die;
	}

	$greylist = fopen ( "/etc/postfix/postgrey_whitelist_clients.local", "r" ) or die ("Can't open \"/etc/postfix/postgrey_whitelist_clients.local\" file");
	while ( ! feof($greylist) ) {
		$temp_info = fgets($greylist);
		$temp_info = str_replace(" ","",$temp_info);
		$temp_info = trim(str_replace("\r\n","",$temp_info));
		if ( preg_match("/^[a-zA-Z0-9]/",$temp_info ) ) {
			$mail_greylist[] = $temp_info;
		}
	}

	$proc_greylist_temp = shell_exec ( "grep 'postgrey' /etc/postfix/main.cf | grep policy 2> /dev/null" );

	if ( $proc_greylist_temp != "" ) {
		$proc_greylist = "OK";
	} else {
		$proc_greylist = "false";
	}


	/*echo "$mail_hostname|mydestination=";
	foreach ( $mail_dest as $temp ) echo "$temp,";
	echo "|mynetworks=";
	foreach ( $mail_clients as $temp ) echo "$temp,";
	echo "$mail_mbox_size|$mail_mesg_size|$mail_mesg_size";
	echo "<br />";
	echo "<br />";

	echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/

?>

