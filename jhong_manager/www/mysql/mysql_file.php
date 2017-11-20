<?php	
	$www = "check";
	include("./mysql_config.php");
	include("./mysql_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "backup" ) {
			$database  = $_REQUEST['database'];

			$checkdata = checkdatabasename($database);

			if ( $checkdata != "OK" ) {
        		        echo "<script>alert('系統無此資料庫名稱，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
        		        die;
        		}

			// 預先防呆一下！
		        $existdata = 0;
		        $datasql = "show databases";
		        $datares = mysql_query($datasql,$link2);
		        while ( $row = mysql_fetch_row ($datares) )  {
		                if ( $row[0] == "$database" ) {
		                        $existdata = 1;
		                }
		        }
		        if ( $existdata == 0 ) {
		                echo "<script>alert('系統無此資料庫名稱，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
		                die;
		        }

			$datacontent = shell_exec ( "mysqldump -u root -p${sql_passwd2} $database " );
			header("Content-type: application/text");
			header("Content-Disposition: attachment; filename=$database.txt");
			echo $datacontent;
		}
	}

?>
