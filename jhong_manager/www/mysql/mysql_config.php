<?php
	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=/index.php>";
		die;
	}

	// check the Mysql connection.
	$sql_server2 = 'localhost';
	$sql_user2   = 'root';
	$sql_passwd2 = '3017162168';
	$sql_data2   = 'mysql';

	$link2 = mysql_connect ( $sql_server2, $sql_user2, $sql_passwd2 );

	if ( ! $link2 ) {
		echo "無法連接資料庫，本功能暫時無法繼續服務";
	} else {
		mysql_query ("SET NAMES utf8");
		mysql_select_db ( $sql_data2, $link2 );
	}
?>
