<?php  
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "網路自動修訂系統時間";
	include("../include/header.php");
	$mytime = date("Y-m-d / H:i:s");

	// Check if user want to do!
	if ( isset ( $_REQUEST['delgo'] ) ){
		$delgo = "yes";
	} else{
		$delgo = "no";
	}

	// OK! user will do this job.
	if ( $delgo == "yes" ){
		$time_server=$_REQUEST['time_server'];
		$mylog = exec("sudo /sbin/ntpdate $time_server &> /dev/null && echo 'OK' || echo 'error'");
		if ( $mylog == "OK" ) {
			$log = exec ( "sudo hwclock -w" );
			$mylogtxt = "修改時間成功";
		}
		if ( $mylog != "OK" ) $mylogtxt = "修改時間失敗了, 請選擇另一部時間伺服器來更新";
		echo "<script>alert('" . $mylogtxt  . "');location.href='${web}/system/sys_info.php';</script>";
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">時間校正(自動)</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form name="news" method="POST" OnSubmit="return myfunc()">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="2">自動校正系統日期與時間</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-lg-2 text-right">現在時間顯示:</td>
                                <td class="col-lg-10"><?php echo $mytime; ?></td>
                            </tr>
                            <tr>
                                <td class="col-lg-2 text-right">選擇 NTP 伺服器並送出:</td>
                                <td class="col-lg-10">
                                    <div class="col-md-4">
                                        <select class="form-control" name="time_server">
                                            <option value='tock.stdtime.gov.tw' >tock.stdtime.gov.tw</option>";
                                            <option value='time.stdtime.gov.tw' >time.stdtime.gov.tw</option>";
                                            <option value='clock.stdtime.gov.tw' >clock.stdtime.gov.tw</option>";
                                            <option value='tick.stdtime.gov.tw' >tick.stdtime.gov.tw</option>";
                                            <option value='time.windows.com' >time.windows.com</option>";
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="hidden" name="delgo" value="yes" />
                                        <button type='submit' class="btn btn-primary" name='button'>送出</button>
                                    </div>
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
	function myfunc(){
		var msg = confirm("你確定要修改時間嗎?\n\n請確認!");
		return msg;
	}
</script>

<?php     include("../include/footer.php"); ?>
