<?php
	// 先設定比較重要的變數部份！這些需要指定才行喔！
	$www 	= '/jhong/jhong_manager/www';
        $web	= '';	
	$sdir	= '/jhong/jhong_manager/www_scripts';
	$img	= '/images';
	$mrtg	= '/mrtg';
	$sambapath='/jhong/jhong_manager/www_sambas';

	// setting up data directories
	$data_www = "/srv/www";
	$source = '/home/JHong/www_scripts';

	// 避免被 MySQL 攻擊，同時處理連線行為
	require ( $www . "/include/sql_injection.php" ); 
	require ( $www . "/include/mysql_config.php" );

	// some important settings for this system.
	date_default_timezone_set('Asia/Taipei');		// Taiwan Time Zone settings.

	// 開始啟動 session 喔！
	session_start();

	// 看看是要登入還是登出的檢查！如果不是登入登出，這個部份就不會被使用到！
	if ( isset ( $_REQUEST['logincheck']) ) {
		$logincheck = $_REQUEST['logincheck'];
		if ( $logincheck == "yes" ) {
			include ("${www}/include/authenticate.php");
		}
		elseif ( $logincheck == "logout" )	{
			session_start();
			session_unset();
			session_destroy();
			echo "<script>location.href='$web/index.php';</script>";
		}
	}

	// 看看使用者是否已經登入了？
	if ( isset ( $_SESSION['username'] ) ) $sql_username = $_SESSION['username'];
	//echo $_SESSION['username'] . " " . $_SESSION['userlevel'];

	// 看看本頁面是否需要身份驗證？包括使用者或者是管理員的身份～
	if ( isset ( $check_admin ) ) {
		if ( $check_admin == "user" ) {
			if ( $_SESSION['userlevel'] != "user" && $_SESSION['userlevel'] != "admin" ) {
				echo "沒有權限可以瀏覽本頁面";
				echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/index.php>";
				die;
			}
		} elseif ( $check_admin == "admin" ) {
			if ( $_SESSION['userlevel'] != "admin" ) {
				echo "沒有權限可以瀏覽本頁面";
				echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/index.php>";
				die;
			}
		}
	}

	// 檢查磁碟陣列卡是否存在
	$check_raid = shell_exec ( "sudo bash -c \"/bin/hptraidconf -u RAID -p hpt query controllers &> /dev/null; res=\\\$?; if [ \\\"\\\$res\\\" == \\\"0\\\"  ]; then echo yes; else echo no; fi \" " );
?>
<!doctype html>
<html>
<head>
	<meta  charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>捷宏資訊有限公司-<?php echo $jhtitle; ?></title>
	<link href="<?php echo $web; ?>/include/bootstrap.css" rel="stylesheet">
	<link href="<?php echo $web; ?>/include/bootstrap-datetimepicker.min.css" rel="stylesheet">
	<link href="<?php echo $web; ?>/include/sticky-footer.css" rel="stylesheet">
	<link href="<?php echo $web; ?>/include/metisMenu.css" rel="stylesheet">
	<link href="<?php echo $web; ?>/include/sb-admin-2.css" rel="stylesheet">
	<link href="<?php echo $web; ?>/include/font-awesome.css" rel="stylesheet" type="text/css">
        <script src="<?php echo $web; ?>/include/jquery.js"></script>
        <script src="<?php echo $web; ?>/include/moment-with-locales.min.js"></script>
        <script src="<?php echo $web; ?>/include/bootstrap.js"></script>
        <script src="<?php echo $web; ?>/include/bootstrap-datetimepicker.min.js"></script>
        <script src="<?php echo $web; ?>/include/metisMenu.js"></script>
        <script src="<?php echo $web; ?>/include/sb-admin-2.js"></script>
</head>
<body>
<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
             </button>
             <a class="navbar-brand" href="<?php echo $web ;?>/"><img class="img-responsive" src="<?php echo  $img ;?>/JHong1.png" width="150" ></a>
	</div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-top-links navbar-right">
            <?php if ($_SESSION['username'] != null && $_SESSION['username'] == $sql_username )  { ?>
                <li>
                    <a href="<?php echo $web; ?>/sql/myprofile.php"><i class="fa fa-user fa-fw"></i><?php echo $sql_username; ?></a>
                </li>
                <!-- /.user -->
    	        <li>
    	            <a href="?logincheck=logout"><i class="fa fa-sign-out fa-fw"></i>登出系統</a>
                </li>
                <!-- /.sign-out -->
            <?php } ?>
            </ul>
            <?php include("${www}/include/menu.php"); ?>
        </div>
        <!-- /.collapse -->
    </nav>

    <!-- Page Content  -->
    <div id="page-wrapper" >



