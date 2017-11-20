<?php     
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "手動來修改系統時間";
	include("../include/header.php"); 
	$mytime = date("Y-m-d / H:i:s");

	// Check if we need to do
	if ( isset ( $_REQUEST['delgo'] ) ){
		$delgo = "yes";
	} else{
		$delgo = "no";
	}

	// Yes! let us do it!
	if ( $delgo == "yes" ){
                $date=$_REQUEST['date'];
		
		if($date == ""){
                        echo "<script>alert('日期時間的欄位未選')</script>";
                }
                else{
			exec("sudo date -s '$date:00'; sudo hwclock -w ");
			echo "<script>alert('修改時間成功!');location.href='${web}/system/sys_info.php';</script>";
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">時間校正(手動)</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form name="news" method="POST" OnSubmit="return myfunc()">
                <div>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="text-center" colspan="2">手動設定</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="col-lg-2 text-right">現在時間顯示:</td>
                                <td class="col-lg-10"><?php echo $mytime; ?></td>
                            </tr>
                            <tr>
                                <td class="col-lg-2 text-right">手動設定時間:</td>
                                <td class="col-lg-10">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker1'>
                                                <input type='text' class="form-control" name="date"/>
                                                <span class="input-group-addon">
                                                    <span class="fa fa-calendar"></span>
                                                </span>
                                            </div>
                                        </div>
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
<script type="text/javascript">
	$(document).ready(function () {
            $('#datetimepicker1').datetimepicker({
                sideBySide: true,
                locale: 'zh-tw',
                format: 'YYYY-MM-DD HH:mm'
            });
	});
        function myfunc(){
        	var msg = confirm("你確定要修改時間嗎?\n\n請確認!");
                return msg;
	}
</script>
<?php     include("../include/footer.php"); ?>
