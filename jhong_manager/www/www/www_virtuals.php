<?php	
	$buttom      = "apache";
	$check_admin = "admin";
	$jhtitle     = "WWW 伺服器的查看虛擬主機設定值";
	include ("../include/header.php");
	include ("./www_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "delete" ) {
			$docroot = $_REQUEST['docroot'];
			$thisline = 0;
			if ( $wwwvnumber > 0 ) {
				for ( $i=1; $i<=$wwwvnumber; $i++ ) {
					$docroot_old = explode ( "/", $www_v_documentroot[$i] );
					if ( $docroot == $docroot_old[3] ) {
						$thisline = $i;
						break;
					}
				}
			}
			// 開始一堆動作在這裡！
			if ( $thisline > 0 && $docroot != "" ) {
				$log = shell_exec ( "sudo bash -c \"sh /jhong/bin/wwwmount.sh umount $www_v_documentroot[$thisline] $docroot $www_v_jhongadmin[$thisline]; rm -r /jhong/www/$docroot; rm /etc/httpd/conf.d/jhong_${docroot}.conf \" " );
				echo "$log <br />";

				// 最終就得要重新啟動 apache 囉！
				$log = exec ( "sudo systemctl restart httpd" );
				echo "<script>alert('刪除完畢，請檢查輸出結果'); location.href='$web/www/www_virtuals.php';</script>";
				die;
			}
		}
	}
?>
	<h1>WWW 伺服器的查看虛擬主機設定值</h1>
	<table class="account_table">
	<tr>
		<th>虛擬主機名</th>
		<th>支援的主機別名</th>
		<th>WWW 的管理員</th>
		<th>管理員的 email</th>
		<th>首頁目錄名稱</th>
		<th style="width:110px;">管理</th>
	</tr>

<?php if ( $wwwvnumber > 0 ) {
	for ( $i=1; $i<=$wwwvnumber; $i++ ) { ?>
	<tr>
		<td><?php echo $www_v_servername[$i]; ?></td>
		<td><?php echo $www_v_serveralias[$i]; ?></td>
		<td><?php echo $www_v_jhongadmin[$i]; ?></td>
		<td><?php echo $www_v_serveradmin[$i]; ?></td>
		<td>/<?php $docroot = explode("/", $www_v_documentroot[$i]); echo $docroot[3]; ?></td>
		<td>
			<a class="table_button" style="padding: 1px 10px; font-size:9pt;" OnClick="return checkdel()"
				href="www_virtuals.php?action=delete&docroot=<?php echo $docroot[3]; ?>">刪除</a>
			<a class="table_button" style="padding: 1px 10px; font-size:9pt;" 
				href="www_modvirtual.php?action=manage&docroot=<?php echo $docroot[3]; ?>">修改</a></td>
	</tr>
<?php 	} 
      } ?>
        </table>

<script>
	function checkdel() {
		return confirm("是否確定刪除此虛擬主機的設定呢？\n\n需注意，連同該主機的網頁也會一併被刪除掉喔！");
	}

</script>
<?php   include("../include/footer.php"); ?>
