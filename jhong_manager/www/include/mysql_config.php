<?php
	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=/index.php>";
		die;
	}

	// check the Mysql connection.
	$sql_server = 'localhost';
	$sql_user   = 'JHong';
	$sql_passwd = '3017162168';
	$sql_data   = 'JHong';

	$link = mysql_connect ( $sql_server, $sql_user, $sql_passwd );

	if ( ! $link ) {
		echo "無法連接資料庫，本系統暫時無法繼續服務";
		die;
	} else {
		mysql_query ("SET NAMES utf8");
		mysql_select_db ( $sql_data, $link );
	}
?>
