<?php
	$buttom		= "account";
	$check_admin 	= "admin";
	$jhtitle     	= "刪除使用者";
	include("../include/header.php"); 

	if ( ! isset ( $_REQUEST['username'] ) ) {
		echo "<script>alert('真抱歉！您並沒有選擇任何的用戶資訊！無法使用本功能！'); location.href='$web/account/user_time.php';</script>";
	}

	$username = $_REQUEST['username'];

	include ("function.php");

	// 確認使用者是誰
	for ($i=1; $i<=$usernumber; $i++) {
		if ( $my_username[$i] == $username ) {
			$userline = $i;			// 使用者在使用者參數陣列中的位置！
			break;
		}
	}

	// 如果有動作要刪除用戶的話，就在這個位置處理了！不然就直接進入單純的顯示畫面中！
	if ( isset ( $_REQUEST['action'] )) {
		if ( $_REQUEST['action'] == "deluser" ) {
			$delhome = $_REQUEST['delhome'];
			$delmail = $_REQUEST['delmail'];
			if ( $my_user_smb[$userline] == "yes" ) {
				$dellog = exec ( "sudo pdbedit -x -u $username" );
			}
			if ( $delmail == "yes" ) {
				$dellog = exec ( "sudo userdel -r mail_$username " );
				$mails = shell_exec(" 	line=\$(grep -n '^${username}:' /etc/aliases | cut -d ':' -f1); 
							if [ \"\$line\" != '' ]; then 
								sudo sed -i \"\$line d\" /etc/aliases; sudo newaliases ;
							fi");
			}
			if ( $delhome == "yes" ) $homeoption = "-r"; else $homeoption = "";
			$dellog = exec ( "sudo userdel $homeoption $username " );
			echo "<script>alert('刪除成功');location.href='$web/account/user_time.php';</script>";
			die;
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">刪除使用者資料</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">刪除使用者：<?php echo $username ?></div>
                <div class="panel-body">
                    <form class="form-horizontal"  method="post" name="removeuserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return checkremoveuser()">
                        <div class="form-group">
                            <label class="control-label col-lg-3">帳號名稱：</label>
                            <div class="col-lg-9">
                                <p class="form-control-static"><?php echo $username ;?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">真實姓名說明：</label>
                            <div class="col-lg-9">
                                <p class="form-control-static"><?php echo $my_user_gecos[$userline] ;?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">主要/原生群組：</label>
                            <div class="col-lg-9">
                                <p class="form-control-static"><?php echo $my_user_groupname[$userline] ; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">其他次要群組：</label>
                            <div class="col-lg-9">
                                <p class="form-control-static">
                                    <?php
                                        for ( $i=1; $i<=$groupnumber; $i++ ) {
                                            foreach ( $my_group_users[$i] as $grouptemp ) {
                                                if ( $grouptemp == $my_user_groupname[$userline] ) echo $my_groupname[$i] . ", ";
                                            }
                                        }
                                    ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">是否具有家目錄：</label>
                            <div class="form-inline col-lg-9">
                                <?php
                                    $log_home = shell_exec( "[ -d $my_user_home[$userline] ] && echo '存在' || echo '不存在' ");
                                    $log_home = str_replace("\n","",$log_home);
                                    if ( $log_home == "存在" ) {
                                        echo "處理方式：<select class='form-control' name='delhome'><option value='no'>保留下來</option><option value='yes' selected='selected'>刪除家目錄　</option></select>";
                                    } else {
                                        echo $log_home;
                                    }
                                    echo " ($my_user_home[$userline])";
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">是否具有郵件用戶支援：</label>
                            <div class="form-inline col-lg-9">
                                <?php
                                    if ( $my_user_mail[$userline] == "yes" ) {
                                        echo "處理方式：<select class='form-control' name='delmail'><option value='no'>保留下來</option><option value='yes' selected='selected'>刪除郵件帳號</option></select>";
                                        echo " <span class='form-control-static'>(選擇刪除,備份郵件會全遺失)</span>";
                                    } else {
                                        echo "<p class='form-control-static'>沒有</p>";
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-5 col-lg-2">
                                <input type="hidden" name="username" value="<?php echo $username; ?>" />
                                <input type="hidden" name="action"   value="deluser" />
                                <button class="btn btn-primary btn-block" type="submit">刪除用戶</button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checkremoveuser() {
	var msg = confirm("你確定要刪除嗎?\n\n請確認!");
	return msg;
	}
</script>
	
<?php	include("../include/footer.php"); ?>
