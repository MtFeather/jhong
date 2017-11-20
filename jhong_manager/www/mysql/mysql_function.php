<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	checkdatabasename($database)	// 回傳底下兩種值～ OK 是這個資料庫名稱沒問題， false 是這個資料庫會有問題！是給系統用的意思。
		OK
		false
	$sysuser			// 字串，資料庫內不要顯示出來的帳號！

*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");

	//echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	//echo "<br />";

	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	$sysuser = "User != 'root' and User != 'JHong' and User != 'roundcube' and User != 'mailwatch' ";

	function checkdatabasename($database) {
		$base[] = 'information_schema';
		$base[] = 'JHong';
		$base[] = 'mysql';
		$base[] = 'performance_schema';
		$base[] = 'roundcube';
		$base[] = 'mailscanner';
		$checking = "OK";
		foreach ( $base as $temp ) {
			if ( $database == $temp ) $checking = "false";
		}
		return $checking;
	}

	/*$checkdata = checkdatabasename($dataname);
	if ( $checkdata == "OK" ) {  // 確認資料庫名稱不重複就進行
	if ( $checkdata != "OK" ) {  // 確認資料庫有重複，常用在拒絕動作！

	}*/
?>

