<?php 
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     ="網路設定";
	include("../include/header.php");

	// Check hostname
	if ( isset ( $_REQUEST['myhostname'] ) ) {
		echo "<script>alert('Start to modify hostname!')</script>";
		$myhostname = $_REQUEST['myhostname']; 
		$mylog = exec ( "sudo hostnamectl set-hostname $myhostname " );
	}

	// Check Network
	if ( isset ( $_REQUEST['device'] ) ) {
		echo "<script>alert('Start to modify Network Parameters!')</script>";
		$device = $_REQUEST['device'];
		$onboot = $_REQUEST['onboot'];
		$method = $_REQUEST['method'];
		$ipaddr = $_REQUEST['ipaddr'];
		$gwaddr = $_REQUEST['gwaddr'];
		$dns    = $_REQUEST['dns'];
		// echo $device . $onboot . $method . $ipaddr . $gwaddr . $dns; // Just check output

		// modify connection type first
		$mylog = exec ( "sudo nmcli connection modify ${device} connection.autoconnect ${onboot}" );

		// get the IP method is dhcp or manual
		if ( $method == "auto" ) {
			// DHCP only need device name and up this device
			$mylog = shell_exec ( "sudo nmcli connection modify ${device} ipv4.method auto ipv4.addresses '' ipv4.gateway '' ipv4.dns '' &&
				 sudo nmcli connection up ${device} " );
			// echo "<br /><br />" . $mylog; // Only check the result
		} else {
			$mylog = shell_exec ( "sudo nmcli connection modify ${device} ipv4.method manual ipv4.addresses '${ipaddr}' ipv4.gateway '${gwaddr}' ipv4.dns '$dns' &&
				 sudo nmcli connection up ${device} " );
			// echo "<br /><br />" . $mylog; // Only check the result.
		}
	}
	// Get the current hostname
	$myhostname = shell_exec( "sudo hostname" );
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux - 網路資訊顯示與修改</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">修改與設定主機名稱</div>
                    <div class="panel-body">
                        <form class="form-horizontal" name="hostnamectl" method="post" OnSubmit="return checkhostname()">
                            <div class="form-group">
                                <label class="control-label col-lg-2">現在的主機名稱:</label>
                                <div class="col-lg-4">
                                    <input type="text" class="form-control" name="myhostname" value="<?php echo $myhostname; ?>"/>
                                </div>
                                <div class="col-lg-4">
                                    <button type="submit" class="btn btn-primary">開始修改主機名稱</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <?php
                    $sys_list = shell_exec("sudo sh $sdir/system/sys_ip.sh");
                    echo $sys_list;
                ?> 
            </div>
            <!-- /.panel-group -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
        function netcheck(){
                var msg = confirm("你確定要修改網路參數嗎？如果正在遠端處理此項任務，你的連線可能會被中斷喔!\n\n請確認!");
                return msg;
        }
        function checkhostname(){
                var msg2 = confirm("你確定要修改主機名稱嗎!\n\n請確認!");
                return msg2;
        }
</script>

<?php   include("../include/footer.php"); ?>
