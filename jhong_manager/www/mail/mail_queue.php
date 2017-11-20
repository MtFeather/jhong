<?php	
	$buttom		= "mail_server";
	$check_admin 	= "admin";
	$jhtitle     	= "Mail Server Queue";
	include ("../include/header.php");
	include ("./mail_function.php");

	$mailqfile = "/dev/shm/jhong/mail_queue.log";
	$log = exec ( "sudo bash -c \" mailq | grep '^[A-Za-z0-9]' | grep -v '^Mail' > $mailqfile  \" " );

	$queuenu = 0;
	$queuefile = fopen ( "$mailqfile", "r" ) or die ("Can't open \"$mailqfile\" file");
	while ( ! feof ( $queuefile ) ) {
		$queue_line = fgets ( $queuefile );
		$queue_row  = explode ( " ", $queue_line );
		if ( trim(str_replace("\r\n","",$queue_row[0])) != "" ) {
			$queuenu = $queuenu + 1;
			$queue_info[$queuenu] = $queue_line;
			$queue_id[$queuenu]   = $queue_row[0];
		}
	}

	if ( isset ( $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];

		if ( $action == "flush" ) {
			$qid = $_REQUEST['qid'];
			$log = exec ( "sudo postqueue -i $qid" );
			echo "<script>alert('已經重送，請檢查列表資訊'); location.href='$web/mail/mail_queue.php';</script>";
		}

		if ( $action == "delete" ) {
			$qid = $_REQUEST['qid'];
			$log = exec ( "sudo postsuper -d $qid" );
			echo "<script>alert('已經刪除，請檢查列表資訊'); location.href='$web/mail/mail_queue.php';</script>";
		}

		if ( $action == "flushall" ) {
			$log = exec ( "sudo postqueue -f" );
			echo "<script>alert('已經全部重送，請檢查列表資訊'); location.href='$web/mail/mail_queue.php';</script>";
		}

		if ( $action == "deleteall" ) {
			$log = exec ( "sudo postsuper -d ALL " );
			echo "<script>alert('已經全部刪除，請檢查列表資訊'); location.href='$web/mail/mail_queue.php';</script>";
		}

	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Mail 伺服器佇列 (Queue) 觀察/管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <caption style="border: inherit; background-color: #F5F5F5; text-align: center;">佇列的觀察/刪除/重送等任務</caption>
                    <thead>
                        <tr>
                            <th class="text-center">佇列全文</th>
                            <th class="text-center">管理任務</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 	
                        if ( $queuenu == 0 ) {
                            echo "<tr><td colspan='2' class='text-center'><span class='text-primary'>沒有任何佇列存在喔！信件傳輸狀態良好</span></td></tr>";
                        } else { 
                            for ( $i=1; $i<=$queuenu; $i++ ) {
                                echo "<tr><td><pre style='border: 0; background-color: transparent;'>$queue_info[$i]</pre></td><td>";
                                echo "<a href='mail_queue.php?action=flush&qid=$queue_id[$i]' class='btn btn-primary' Onclick='return checkflush()'>重送</a>";
                                echo "<a href='mail_queue.php?action=delete&qid=$queue_id[$i]' class='btn btn-danger' Onclick='return checkdelete()'>刪除</a> ";
                                echo "</td></tr>";
                        }
                    ?>
                        <tr>
                            <td colspan="2" class="text-center;">
                                <a href='mail_queue.php?action=flushall' class='btn btn-primary' Onclick='return checkflushall()' >全部重送</a>
                                <a href='mail_queue.php?action=deleteall' class='btn btn-primary' Onclick='return checkflushall()' >全部刪除</a>
                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->      
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<script>
	function checkflush(f) {
		return confirm("是否確定重送這信件佇列？");
	}
	function checkdelete(f) {
		return confirm("是否確定刪除這信件佇列？");
	}
	function checkflushall(f) {
		return confirm("是否確定重送全部的信件佇列？");
	}
	function checkdeleteall(f) {
		return confirm("是否確定刪除全部的信件佇列？");
	}
</script>

<?php   include("../include/footer.php"); ?>
