<?php
	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}
?>

<?php
	//連接資料庫
	//只要此頁面上有用到連接MySQL就要include它
	$username = $_REQUEST['username'];
	$pw       = sha1($_REQUEST['pw']);

	if ( $username == null || $pw == null ) {
		$login_msg = "請輸入帳密喔!";
	} else {
		//搜尋資料庫資料
		$sql = "SELECT username,password,level FROM j_user where username = '$username';";
		$result = mysql_query($sql);
		$row =@mysql_fetch_row($result);
		if( $row[0] == $username && $row[1] == $pw )
		{
        		$_SESSION['username'] = $username;
			if ( $row[2] == 1 ) {
	        		$_SESSION['userlevel'] = "admin";
			} else {
	        		$_SESSION['userlevel'] = "user";
			}
		}
		else
		{
			$login_msg="登入失敗";
		}
	}
?>
