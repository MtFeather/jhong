<?php 
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "系統狀態與開關機";
	include("../include/header.php");

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "close" ) {
			$service = $_REQUEST['service'];
			$log = exec ( "sudo systemctl stop $service" );
		}
		if ( $_REQUEST['action'] == "open" ) {
			$service = $_REQUEST['service'];
			$log = exec ( "sudo systemctl restart $service" );
		}
		if ( $_REQUEST['action'] == "reboot" ) {
			$log = shell_exec ( "sudo bash -c \" echo \\\"sleep 10s; reboot &> /dev/null \\\" | at now \"" );
			echo "This machine will reboot new! wait a few minutes, please.";
			include("../include/footer.php");
			die;
		}
		if ( $_REQUEST['action'] == "poweroff" ) {
			$log = exec ( "sudo poweroff" );
		}
	}

	$s_nu = 7;
	$s_show[1] = "Samba 檔案伺服器";	$s_name[1] = "smb.service"; 		$s_link[1] = "../samba/samba_status.php";
	$s_show[2] = "DNS 領域伺服器";		$s_name[2] = "named-chroot.service"; 	$s_link[2] = "../dns/zone_status.php";
	$s_show[3] = "FTP 檔案伺服器";		$s_name[3] = "vsftpd.service"; 		$s_link[3] = "../ftp/ftp_status.php";
	$s_show[4] = "WWW 網頁伺服器";		$s_name[4] = "httpd.service"; 		$s_link[4] = "../www/www_status.php";
	$s_show[5] = "Mail 寄件電郵伺服器";	$s_name[5] = "MailScanner.service";	$s_link[5] = "../mail/mail_status.php";
	$s_show[6] = "Mail 收件電郵伺服器";	$s_name[6] = "dovecot.service";		$s_link[6] = "../mail/mail_status.php";
	$s_show[7] = "SQL 資料庫伺服器";	$s_name[7] = "mariadb.service";		$s_link[7] = "../mysql/mysql_status.php";

	for ($i=1; $i<=$s_nu; $i++ ) {
		$log = shell_exec ( "sudo bash -c \" testing=\\\$( systemctl status $s_name[$i] | grep active | grep running); if [ \\\"\\\$testing\\\" != \\\"\\\" ]; then echo \\\"ok\\\"; else echo \\\"no\\\"; fi  \"" );
		if ( trim($log) == "ok" ) {
			$trstatus[$i] = "";
			$s_state[$i] = "<i class='fa fa-circle' style='color: #5CB85C;'></i>正常";
			$opengo[$i] = "<a href='sys_service.php?action=close&service=$s_name[$i]' class='btn btn-primary' OnClick='return closecheck()' >關閉服務</a>";
		}  else  {
			$trstatus[$i] = "class='warning'";
			$s_state[$i] = "<i class='fa fa-circle' style='color: #C9302C;'></i>未啟動";
			$opengo[$i] = "<a href='sys_service.php?action=open&service=$s_name[$i]' class='btn btn-primary' OnClick='return opencheck()' >啟動服務</a>";
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">系統服務狀態與系統開關機</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">服務名稱</th>
                            <th class="text-center">目前狀態</th>
                            <th class="text-center">開啟/關閉</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
	for ( $i=1; $i<=$s_nu; $i++ ) {
		echo "<tr ${trstatus[$i]}><td>$s_show[$i]</td><td class='text-center'>$s_state[$i]</td>";
		echo "<td class='text-center'><a href='$s_link[$i]' class='btn btn-primary'>觀察狀態</a> ";
		echo "$opengo[$i]</td></tr>";

	}
?>
	<tr><td>全系統運作情況</td><td class='text-center'>-</td><td class='text-center'>
			<a href='sys_service.php?action=reboot' class='btn btn-danger' OnClick='return rebootcheck()' >重新開機</a>
			<a href='sys_service.php?action=poweroff' class='btn btn-danger' OnClick='return poweroffcheck()' >關閉機器</a>
		
	</tr>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->	
<script type="text/javascript">
	function closecheck() {
		return confirm("是否確定要關閉此服務呢？切記不要有用戶在線上喔！");
	}
	function opencheck() {
		return confirm("是否確定要要開啟此服務呢？");
	}
	function rebootcheck() {
		var check1 = confirm("是否確定將系統重新開機？");
		if ( check1 == true ) {
			return confirm("你確定不會後悔？");
		} else {
			return false;
		}
	}
	function poweroffcheck() {
		var check1 = confirm("是否確定進行全系統關機？若你不在電腦前，未來需要在機器前面按下 power 才能夠啟動系統喔！");
		if ( check1 == true ) {
			return confirm("你確定不會後悔？");
		} else {
			return false;
		}
	}

</script>

<?php   include("../include/footer.php"); ?>
