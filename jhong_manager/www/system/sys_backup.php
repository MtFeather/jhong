<?php 
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "系統備份功能";
	include("../include/header.php");

	//$log = exec ( "sudo bash -c \"   \" " );

	$checking = exec ( "sudo sh $sdir/system/sys_backup.sh check " );
	$checking = trim(str_replace("\r\n","",$checking));
	if ( $checking != "OK" ) {
		echo "目前有其他複製行為正在處理中！<br />請稍後再進行此項任務！";
		include ( "../include/footer.php" );
		die;
	}

	$dir_show[1] = "用戶家目錄"; 		$dir_name[1] = "/home"; 		$dir_value[1] = "home";
	$dir_show[2] = "網芳之目錄"; 		$dir_name[2] = "/jhong/samba"; 		$dir_value[2] = "samba";
	$dir_show[3] = "WWW 之目錄"; 		$dir_name[3] = "/jhong/www"; 		$dir_value[3] = "www";
	$dir_show[4] = "用戶新建郵件夾目錄"; 	$dir_name[4] = "/var/spool/mail"; 	$dir_value[4] = "www";
	$dir_nu = 4;

	if ( isset ( $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action']; 
	} else {
		$action = "disk"; 
	}

	$outfile = "/dev/shm/jhong/backup_message.log";

	if ( $action == "disk" ) {
		$log = exec ( "sudo bash -c \" sh $sdir/system/sys_backup.sh disk > $outfile   \" " );
		$mesg = "";
		$myfile = fopen($outfile,"r") or die ("Can't open \"$outfile\" file");
		while ( ! feof($myfile) ) {
			$temp_line = fgets($myfile);
			if ( preg_match ("/^NAME/",$temp_line ) ) {
				$mesg = $mesg . "   " . $temp_line ;
			} else {
				$temp_row = explode ( " ", $temp_line);
				if ( trim($temp_row[0]) != "" ) {
					$mesg = $mesg . 
						"<input type='radio' name='disk' value='$temp_row[0]' /> " . $temp_line ;
				}
			}
		}
	}

	if ( $action == "partition" ) {
		$disk = $_REQUEST['disk'];
		if ( trim($disk) == "" ) {
			echo "<script>alert('沒有選擇磁碟喔！');location.href='${web}/system/sys_backup.php';</script>";
			die;
		}
		$log = exec ( "sudo bash -c \" sh $sdir/system/sys_backup.sh partition $disk > $outfile   \" " );
		$mesg = "";
		$myfile = fopen($outfile,"r") or die ("Can't open \"$outfile\" file");
		while ( ! feof($myfile) ) {
			$temp_line = fgets($myfile);
			if ( preg_match ("/^NAME/",$temp_line ) ) {
				$mesg = $mesg . "   " . $temp_line ;
			} else {
				$temp_row = explode ( " ", $temp_line);
				if ( trim($temp_row[0]) != "" ) {
					$mesg = $mesg . 
						"<input type='radio' name='partition' value='$temp_row[0]' /> " . $temp_line ;
				}
			}
		}
	}

	if ( $action == "filesystem" ) {
		$partition = $_REQUEST['partition'];
		if ( trim($partition) == "" ) {
			echo "<script>alert('沒有選擇分割槽喔！');location.href='${web}/system/sys_backup.php';</script>";
			die;
		}
		for ( $i=1; $i<=$dir_nu; $i++) {
			$dir_du[$i] = 0;
			$dir_du[$i] = exec ( "sudo du -sm $dir_name[$i] | col -x | cut -d ' ' -f 1" );
		}
	}

	if ( $action == "go" ) {
		$partition = $_REQUEST['partition'];
		$source    = $_REQUEST['source'];
		$debug	   = $_REQUEST['debug'];
		if ( trim($partition) == "" ) {
			echo "<script>alert('沒有選擇分割槽喔！');location.href='${web}/system/sys_backup.php';</script>";
			die;
		}
		$snu = 0;
		foreach ( $source as $temp ) {
			if (trim($temp) != "" ) {
				$snu = $snu + 1;
				for ( $i=1; $i<=$dir_nu; $i++ ) {
					if ( $temp == $dir_value[$i] ) $sources = "$sources $dir_name[$i]";
				}
			}
		}
		if ( $snu == 0 ) {
			echo "<script>alert('沒有選擇要備份的資料喔！');location.href='${web}/system/sys_backup.php';</script>";
			die;
		}

		//echo "sudo bash -c \" sh $sdir/system/sys_backup.sh copy $partition $sources &> $outfile   \" " ;
		echo "備份開始：";
		echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
		echo "<br /><br />";
		$log = exec ( "sudo bash -c \" sh $sdir/system/sys_backup.sh copy $partition $sources &> $outfile   \" " );
		echo "備份完成：";
		echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
		echo "<br /><br />";
		if ( $debug == "yes" ) {
			$msg = shell_exec ( "cat $outfile" );
			echo "<pre>$msg</pre>";
		}
		die;
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">以外接設備來備份本系統的用戶資料</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form method="post" action="sys_backup.php" OnSubmit="return checkme(this)" >
                <div class="panel-group">

<?php	if ( $action == "disk" ) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">第一步：目前系統可用的磁碟容量與狀態</div>
                        <div class="panel-body">
	                    <pre style="border: 0; background-color: transparent;"><?php echo $mesg; ?></pre>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <input type="hidden" name="action" value="partition" />
                            <button class="btn btn-primary" type="submit">下一步：查閱選擇的磁碟內容</button>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
<?php	} ?>

<?php	if ( $action == "partition" ) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">第二步：磁碟機： <?php echo $disk; ?> 的可用分割槽狀態</div>
                        <div class="panel-body">
                            <pre style="border: 0; background-color: transparent;"><?php echo $mesg; ?></pre>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <input type="hidden" name="action" value="filesystem" />
                            <button class="btn btn-primary" type="submit">下一步：選擇備份資料</button>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
<?php	} ?>

<?php	if ( $action == "filesystem" ) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">第三步：選擇你想要備份的資料</div>
                        <div class="panel-body">
                            <pre style="border: 0; background-color: transparent;">欲備份資料 (此目錄所佔用的容量)
<?php
                                for ( $i=1; $i<=$dir_nu; $i++) {
                                    echo "<input type='checkbox' name='source[]' value='$dir_value[$i]' /> $dir_show[$i] ($dir_du[$i] MBytes)\n";
                                }
?>
                            </pre>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
	                    <input type="hidden" name="partition" value="<?php echo $partition;  ?>" />
                            <input type="hidden" name="action" value="go" />
                            <button class="btn btn-primary" type="submit">下一步：開始備份</button>
		            <input type="checkbox" name="debug" value="yes" />顯示 debug 資訊
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
<?php	} ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            詳細資料說明如下：
	                    <ul>
                                <li>NAME：磁碟裝置名稱</li>
                                <li>SIZE：這個磁碟裝置的容量大小</li>
                                <li>TYPE：是磁碟 (disk) 、分割槽 (partition) 還是快閃碟(rom)</li>
                                <li>VENDOR：製造商</li>
                                <li>RM：是否為可抽取式裝置，例如 USB 等。若為 1 則是可抽取的裝置</li>
                                <li>MODEL：詳細的裝置型號</li>
                                <li>FSTYPE：檔案系統資訊</li>
                                <li>MOUNTPOINT：是否有掛載，且掛載於何處的意思</li>
                            </ul>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.panel-group  -->
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

<script>
	function checkme(f) {
		return confirm("是否確定所選擇的項目？？");
	}

</script>

<?php   include("../include/footer.php"); ?>
