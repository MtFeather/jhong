<?php
	$buttom		= "account";
	$check_admin 	= "admin";
	$jhtitle      	= "使用者加入/移出群組的方法 ";
	include("../include/header.php");

	// 先處理一下，如果沒有群組的設定，那就不要給予執行的功能
	if ( isset ( $_REQUEST['group'] ) ) {
		$group = $_REQUEST['group'];
		$log = exec ( "sudo grep '^${group}:' /etc/group &> /dev/null && echo 'ok' || echo 'non' " );
		if ( $log != 'ok' ) {
			echo "沒有 '$group' 這群組！無法執行！"; 
			die;
		}
	} else {
		echo "沒有 '$group' 這群組！無法執行！"; 
		die;
	}
 
	if ( isset ( $_REQUEST['delgo'] ) ){
		$delgo = "yes";
	} else{
		$delgo = "no";
	} 

	// 處理一下新增用戶到群組的功能
	if ( isset ( $_REQUEST['addusergo'] ) ) {
		if ( $_REQUEST['addusergo'] == "yes" ) {
			$addusers = $_REQUEST['adduser'];
			$group    = $_REQUEST['group'];
			foreach ( $addusers as $thisuser ) {
				$log = exec ( "sudo usermod -a -G $group $thisuser 2>&1 " );
			}
		}
	}

	// 處理一下將用戶移除群組的功能
	if ( isset ( $_REQUEST['removeusergo'] ) ) {
		if ( $_REQUEST['removeusergo'] == "yes" ) {
			$removeusers = $_REQUEST['removeuser'];
			$group       = $_REQUEST['group'];
			foreach ( $removeusers as $thisuser ) {
				$log = exec ( "sudo gpasswd -d $thisuser $group 2>&1 " );
			}
		}
	}
	include ("function.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">加入/移除使用者到 <?php echo ${group}; ?> 群組</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">將 <?php echo $group ?> 群組作為原生群組的用戶(無法移除)</div>
                    <div class="panel-body text-center">
                        <p>底下的帳號將無法直接移出本群組。須刪除此帳號或者是重新修改此帳號之原生群組，方可移出。點選帳號將可進入帳號列表與控制之連結頁面。</p>
                        <?php
                            $tempyes = 0;
                            if ( $usernumber != 0 ) {
                                for ( $i=1; $i<=$usernumber; $i++ ) {
                                    if ( $my_user_groupname[$i] == $group ) {
                                        $tempyes = $tempyes + 1;
                                        echo "<a class='lead' href='${web}/account/user_time.php?username=${my_username[$i]}'>${my_username[$i]}</a>, ";
                                    }
                                }
                            }
                            if ( $tempyes == 0 ) {
                                echo "無主要群組用戶";
                            }
                        ?>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">將用戶加入 <?php echo $group ?> 群組</div>
                    <div class="panel-body">
                        <form method="post" name="adduserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return checkadduser()">
                        <?php
                            // 先取得這個群組的所有資訊，就是要找到這個群組的號碼！
                            if ( $groupnumber != 0 ) {
                                for ( $i=1; $i<=$groupnumber; $i++) {
                                    if ( $group == $my_groupname[$i] ) {
                                        $groupline = $i;
                                        break;
                                    }
                                }
                            }

                            // 再來找出不是初始群組，也沒有加入次要群組的用戶
                            $tempyes = 0;
                            if ( $usernumber != 0 ) {
                                echo "<div class='row'><div class='col-lg-2'>";
                                $thisline = 0;
                                for ( $i=1; $i<=$usernumber; $i++ ) {
                                    if ( $my_user_groupname[$i] != $group ) {
                                        $thischeck1 = "no";
                                        foreach ( $my_group_users[$groupline] as $grouptemp ) {
                                            if ( $my_username[$i] == $grouptemp ) $thischeck1 = "yes";
                                        }
                                        if ( $thischeck1 == "no" ) {
                                            $tempyes = $tempyes + 1;
                                            if ( $my_user_bk[$i] == "no" ) {
                                                $readonly = '';
                                                $bk1=''; $bk2='';
                                                $thisname = "adduser[]";
                                            } else {
                                                $readonly = "disabled";
                                                $bk1="<strong class='text-danger'>";
                                                $bk2="</strong>";
                                                $thisname = "";
                                            }
                                            $thisline = $thisline + 1;
                                            echo "<div class='checkbox $readonly'><label><input type='checkbox' name='${thisname}' value='${my_username[$i]}' $readonly />${bk1}${my_username[$i]}${bk2}</label></div>";
                                            if ( $thisline == 10 ) {
                                                echo "</div>";
                                                echo "<div class='col-lg-2'>";
                                                $thisline = 0;
                                            }
                                        }
                                    }
                                }
                                echo "</div></div>";
                            }
                            if ( $tempyes == 0 ) {
                                echo "無用戶";
                            }
                        ?>
                            <div class="row text-center">
                                <input type="hidden" name="group" value="<?php echo $group ?>" />
                                <input type="hidden" name="addusergo" value="yes" />
                                <button type="submit" class="btn btn-primary">開始加使用者進 <?php echo $group ?> 群組</button>
                                <button type="button" class="btn btn-primary" onclick="adduseryes();">全勾選</button>
                                <button type="button" class="btn btn-primary" onclick="addusernot();">全取消</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">將用戶移出 <?php echo $group ?> 群組</div>
                    <div class="panel-body">
                        <form method="post" name="removeuserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return checkremoveuser()">
                            <?php
                                // 先取得這個群組的所有資訊，就是要找到這個群組的號碼！
                                if ( $groupnumber != 0 ) {
                                    for ( $i=1; $i<=$groupnumber; $i++) {
                                        if ( $group == $my_groupname[$i] ) {
                                            $groupline = $i;
                                            break;
                                        }
                                    }
                                }

                                // 再來找出不是初始群組，且有加入次要群組的用戶
                                $tempyes = 0;
                                if ( $usernumber != 0 ) {
                                    echo "<div class='row'><div class='col-lg-2'>";
                                    $thisline = 0;
                                    for ( $i=1; $i<=$usernumber; $i++ ) {
                                        if ( $my_user_groupname[$i] != $group ) {
                                            $thischeck1 = "no";
                                            foreach ( $my_group_users[$groupline] as $grouptemp ) {
                                                if ( $my_username[$i] == $grouptemp ) $thischeck1 = "yes";
                                            }
                                            if ( $thischeck1 == "yes" ) {
                                                $tempyes = $tempyes + 1;
                                                if ( $my_user_bk[$i] == "no" ) {
                                                    $readonly = '';
                                                    $bk1=''; $bk2='';
                                                    $thisname = "removeuser[]";
                                                } else {
                                                    $readonly = "disabled";
                                                    $bk1="<strong class='text-danger'>";
                                                    $bk2="</strong>";
                                                    $thisname = "";
                                                }
                                                $thisline = $thisline + 1;
                                                echo "<div class='checkbox $readonly'><label><input type='checkbox' name='${thisname}' value='${my_username[$i]}' $readonly />${bk1}${my_username[$i]}${bk2}</label></div>";
                                                if ( $thisline == 10 ) {
                                                    echo "</div>";
                                                    echo "<div class='col-lg-2'>";
                                                    $thisline = 0;
                                                }
                                            }
                                        }
                                    }
                                    echo "</div></div>";
                                }
                                if ( $tempyes == 0 ) {
                                    echo "無用戶";
                                }
                            ?>
                            <div class="row text-center">
                                <input type="hidden" name="group" value="<?php echo $group ?>" />
                                <input type="hidden" name="removeusergo" value="yes" />
                                <button type="submit" class="btn btn-primary">使用者移出 <?php echo $group ?> 群組</button>
                                <button type="button" class="btn btn-primary" onclick="removeuseryes();">全勾選</button>
                                <button type="button" class="btn btn-primary" onclick="removeusernot();">全取消</button>
                            </div>
                        </form> 
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.panel-group  -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12 text-center">
	    <b>用戶名是<font class="text-danger">紅色</font>字體代表現在是被凍結狀態，要先解除凍結用戶名才能移除!</b><br />
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checkadduser() {
		var $msgcheckadduser = confirm("確定將選擇的使用者加入到 <?php echo $group; ?> 群組中？\n\n請確認");
		return $msgcheckadduser;
	}

	function adduseryes() {
		var checkadduseryes = document.getElementsByName("adduser[]");
		for(var i=0;i<checkadduseryes.length;i++){
			checkadduseryes[i].checked=true;
        	}
	}

	function addusernot() {
		var checkaddusernot = document.getElementsByName("adduser[]");
		for(var i=0;i<checkaddusernot.length;i++){
			checkaddusernot[i].checked=false;
        	}
	}

	function checkremoveuser() {
		var $msgcheckremoveuser = confirm("確定將選擇的使用者移出 <?php echo $group; ?> 群組？\n\n請確認");
		return $msgcheckremoveuser;
	}

	function removeuseryes() {
		var checkremoveuseryes = document.getElementsByName("removeuser[]");
		for(var i=0;i<checkremoveuseryes.length;i++){
			checkremoveuseryes[i].checked=true;
        	}
	}

	function removeusernot() {
		var checkremoveusernot = document.getElementsByName("removeuser[]");
		for(var i=0;i<checkremoveusernot.length;i++){
			checkremoveusernot[i].checked=false;
        	}
	}
</script>


<?php	include("../include/footer.php"); ?>
