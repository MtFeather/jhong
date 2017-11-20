<?php	
	$buttom		= "ftp_server";
	$check_admin 	= "admin";
	$jhtitle     	= "FTP服務";
	include("../include/header.php");

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$nochroot = $_REQUEST['nochroot'];
			$msg = "";
			foreach ( $nochroot as $temp ) { 
				if ( $msg == "" ) $msg = $temp; else $msg = "${msg}\n$temp";
			}
			if ( $msg != "" ) {
				$log = exec ( "sudo bash -c \" echo '$msg' > /etc/vsftpd/chroot_list \" " );
			} else {
				$log = exec ( "sudo rm /etc/vsftpd/chroot_list; sudo touch /etc/vsftpd/chroot_list " );
			}
			echo $log;
		}
	}

	include("./ftp_function.php");
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">FTP 服務資訊</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form method="post" action="<?php echo $web; ?>/ftp/ftp_show.php" OnSubmit="return checkgo()">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>帳號名稱</th>
                                <th>使用容量 (MB)</th>
                                <th>有無chroot權限</th>
                                <th>勾選取消 chroot</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            for ( $i=1; $i<=$usernumber; $i++ ) {
                                if ( $my_user_bk[$i] == "no" ) {
                                    if ( $my_user_ftpchroot[$i] == "no" ) {
                                        $checked = "checked='checked'";
                                        $mystyle = "class='warning'";
                                    } else {
                                        $checked = '' ;
                                        $mystyle = '' ;
                                    }
                                    echo "<tr $mystyle><td>$my_username[$i]</td><td>$my_user_home_du[$i]</td><td>$my_user_ftpchroot[$i]</td>";
                                    echo "<td><input type='checkbox' name='nochroot[]' value='$my_username[$i]' $checked /></td></tr>";
                                }
                            }
                        ?>
                            <tr>
                                <td colspan="3"></td>
                                <td>
                                    <input type="hidden" name="action" value="yes" />
                                    <button type="submit" class="btn btn-primary">開始修改</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->	
<script>
	function checkgo() {
		return confirm("是否確認要開始處理取消選擇的用戶之 chroot 功能");
	}
</script>

<?php   include("../include/footer.php"); ?>
