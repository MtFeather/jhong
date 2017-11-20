<?php
	$buttom="system";
	$check_admin = "admin";
        $jhtitle     = "工作排程";
	include("../include/header.php"); 

	if ( isset ( $_REQUEST['delgo'] ) ){
		$delgo = "yes";
	} else{
		$delgo = "no";
	}

	if ( isset ($_REQUEST['delnu']) ){
		$delnu = $_REQUEST['delnu'] ;
		exec ("sudo sed -i '${delnu}d' /etc/cron.d/jhong");
		echo "<script>alert('刪除成功!');location.href='${web}/system/sys_crontab.php';</script>";
	}

	if ( $delgo == "yes" ){
		$minute=$_REQUEST['minute'];
		$time=$_REQUEST['time'];
		$day=$_REQUEST['day'];
		$month=$_REQUEST['month'];
		$week=$_REQUEST['week'];
		$content=str_replace("'","''",$_REQUEST['content']);
		$mymsg="$minute $time $day $month $week root $content\n";

		if($content == ""){
			echo "<script>alert('沒輸入排程內容')</script>";
		} else {
			exec("sudo touch /dev/shm/cronlog");
			exec("sudo chmod 666 /dev/shm/cronlog");
			$fh = fopen("/dev/shm/cronlog","w") or die ("Can't open \"/dev/shm/cronlog\" file");
			$text = <<<_END
$minute $time $day $month $week root $content\n
_END;
			fwrite($fh, $text);
			fclose($fh);
			exec("sudo bash -c \" cat /dev/shm/cronlog >> /etc/cron.d/jhong \" ");
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">系統工作排程處理</h1>
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
                            <th class="text-center" colspan="7">既有之工作排程狀態</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="text-center">
                            <td class="col-lg-1">月</td>
                            <td class="col-lg-1">日</td>
                            <td class="col-lg-1">週</td>
                            <td class="col-lg-1">時</td>
                            <td class="col-lg-1">分</td>
                            <td class="col-lg-6">工作排程</td>
                            <td class="col-lg-1">設定</td>
                        </tr>
                        <?php
                            $crontab=shell_exec("sh  $sdir/system/sys_crontab.sh");
                            echo  "$crontab";
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <form class="" name="crontab" method="POST" OnSubmit="return myfunc()">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th class="text-center" colspan="2">新增工作排程</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="col-lg-2 text-right">預計執行時間:</td>
                        <td class="col-lg-10 form-inline">
                            <div class="form-group">
                                <select class="form-control" name="month">
                                    <option value='*' >*</option>
                                    <?php for($a = 1 ; $a < 13;$a++){
                                        echo "<option value='$a' >$a </option>";
                                    }?>
                                </select>
                                <label>月,</label>
                            </div>
                            <div class="form-group">
                            <select class="form-control" name="day">
                                <option value='*' >*</option>
                                <?php for($a = 1 ; $a < 32;$a++){
                                    echo "<option value='$a' >$a </option>";
                                }?>
                            </select>
                            <label>日,</label>
                            </div>
                            <div class="form-group">
                            <select class="form-control" name="week">
                                <option value='*' >*</option>
                                <?php for($a = 1 ; $a < 8;$a++){
                                    echo "<option value='$a' >$a </option>";
                                }?>
                            </select>
                            <label>周,</label>
                            </div>
                            <div class="form-group">
                            <select class="form-control" name="time">
                                <option value='*' >*</option>
                                <?php for($b = 0 ; $b < 24;$b++){
                                    echo "<option value='$b' >$b </option>";
                                }?>
                            </select>
                            <label>時,</label>
                            </div>
                            <div class="form-group">
                            <select class="form-control" name="minute">
                                <option value='*' >*</option>
                                <?php for($a = 0 ; $a < 60;$a++){
                                    echo "<option value='$a' >$a </option>";
                                }?>
                            </select>
                            <label>分 *號代表任何時刻都接受</label>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="col-lg-2 text-right">執行指令內容:</td>
                        <td class="col-lg-12">
                            <input type="text" class="form-control" name="content" id="content" />
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <input type="hidden" name="delgo" value="yes" />
                            <div class="col-lg-offset-5 col-lg-2">
                                <button type='submit' name='button'  class='btn btn-primary btn-block'>送出</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </form>
</div>
<!-- /.container-fluid -->
<script>
        function myfunc(){
		if ( crontab.content.value == '' ) {
			alert("指令內容是空的,不可執行喔!!");
			return false;
		}
        	var msg = confirm("你確定要新增排程嗎?\n\n請確認!");
		return msg;
	}

	function delfunc() {
		var msg = confirm("是否要確定刪除這一條工作呢?");
		return msg;
	}
</script>

<?php     include("../include/footer.php"); ?>
