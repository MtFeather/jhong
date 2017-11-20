<?php
	$buttom		= "account";
	$check_admin 	= "admin";
        $jhtitle     	= "修改帳號參數";
	include("../include/header.php");

	if ( ! isset ( $_REQUEST['username'] ) ) {
		echo "<script>alert('真抱歉！您並沒有選擇任何的用戶資訊！無法使用本功能！'); location.href='$web/account/user_time.php';</script>";
	}

	$username = $_REQUEST['username'];

	include ( "function.php" );

	// 確認使用者是誰
	for ($i=1; $i<=$usernumber; $i++) {
		if ( $my_username[$i] == $username ) {
			$raw_uid	= $my_user_uid[$i];
			$raw_groupid	= $my_user_groupid[$i];
			$raw_groupname	= $my_user_groupname[$i];
			$raw_name	= $my_user_gecos[$i];
			$raw_home	= $my_user_home[$i];
			if ( str_replace("\n","",$my_user_shell[$i]) == "/sbin/nologin" ) $raw_shell = "no"; else $raw_shell = "yes";
			$raw_bk		= $my_user_bk[$i];
			$raw_mail	= $my_user_mail[$i];
			$raw_smb	= $my_user_smb[$i];
			$raw_allgroup	= "";
			for ( $j=1; $j<=$groupnumber; $j++ ) {
				foreach ( $my_group_users[$j] as $grouptemp ) {
					if ( $grouptemp == $username ) {
						if ( $raw_allgroup == "" ) 
							$raw_allgroup = $my_groupname[$j]; 
						else 
							$raw_allgroup = $raw_allgroup . "," . $my_groupname[$j]; 
					}
				}
			}
			break;
		}
	}

	//echo "$raw_uid|$raw_groupid|$raw_groupname|$raw_name|$raw_home|$raw_shell|$raw_bk|$raw_mail|$raw_smb|$raw_allgroup<br />";
	//echo "raw|$username|||$raw_name|no|$raw_smb|$raw_mail|$raw_allgroup<br />  ";

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$username  = $_REQUEST['username'];
			$password1 = $_REQUEST['password1'];
			$password2 = $_REQUEST['password2'];
			$name      = $_REQUEST['name'];
			$oldgroup  = $_REQUEST['oldgroup'];
			$groups    = $_REQUEST['groups'];
			$shell     = $_REQUEST['shell'];
			if ( $shell == "no" ) $option_shell = "-s /sbin/nologin"; else $option_shell = "";
			$samba     = $_REQUEST['samba'];
			$mail      = $_REQUEST['mail'];

			// 一些基本控制碼
			$mystate    = "ok";		// 判斷動作是否正確
			$jhong_log  = "";		// 判斷有沒有訊息書出
			$mypw       = "";		// 判斷是否有改密碼？所以可能會需要修訂其他密碼
			$needpw     = "";		// 是否需要修訂密碼呢？

			$allgroup = "";
			if ( count($oldgroup) > 0 ) {
				foreach ( $oldgroup as $grouptemp ) {
					if ( $allgroup == "" ) $allgroup = $grouptemp; else $allgroup = $allgroup . "," . $grouptemp;
				}
			}
			if ( $groups != "no" ) {
				if ( $allgroup == "" )  $allgroup = $groups; else $allgroup = $allgroup . "," . $groups;
			}

			// 單純顯示一下所需要的資料是否正確？
			//echo "raw|$username|||$raw_name|no|$raw_smb|$raw_mail|$raw_allgroup<br />  ";
			//echo "new|$username|$password1|$password2|$name|$groups|$samba|$mail|$allgroup <br /> ";

			// 先檢查是否個人帳號有參數有誤差？若有不同，就需要重新設定才行。
			$option_name   = "";		// 是否有改真實姓名
			$option_shell  = "";		// 是否要改登入情況
			$option_groups = "";		// 是否要改不同的次要群組支援
			$pw = rand (1000000,9999999);	// 若有需要暫時的密碼，使用這個亂數計算出來

			if ( $raw_name != $name && $name != "" ) $option_name = "-c '$name'"; 
			if ( $raw_shell != $shell ) {
				if ( $shell == "yes" ) $option_shell = "-s /bin/bash";
				if ( $shell == "no"  ) $option_shell = "-s /sbin/nologin";
			}
			if ( $raw_allgroup != $allgroup ) {
				if ( $allgroup == "" ) $option_groups = "-G $username";
				if ( $allgroup != "" ) $option_groups = "-G $allgroup";
			}
			if ( $option_name != "" || $option_shell != "" || $option_groups != "" ) {
				//echo "sudo usermod $option_name $option_shell $option_groups $username <br />";
				$log = exec ("sudo usermod $option_name $option_shell $option_groups $username");
			}

			if ( $raw_smb != $samba ) {
				if ( $samba == "yes" ) {
					//echo "echo  -e '$pw\n$pw\n'  | sudo pdbedit -a $username -t <br />";
					$log = exec ( "echo  -e '$pw\n$pw\n'  | sudo pdbedit -a $username -t" );
					$needpw = "yes";
				} else {
					$log = exec ( "sudo pdbedit -x $username " );
				}
			}

			if ( $raw_mail != $mail ) {
				if ( $mail == 'yes' ) {
					$log = exec ( "sudo useradd -g backupmail -s /sbin/nologin -c $username mail_$username" );
					$log = exec ( "echo $pw | sudo passwd --stdin mail_$username" );
					$log = exec ( "sudo bash -c \" echo $username: $username,mail_$username >> /etc/aliases; newaliases \" " );
					$needpw = "yes";
				} else {
					$log = exec ( "sudo userdel -r mail_$username" );
					$log = exec ( "line=\$(grep -n '^${username}:' /etc/aliases | cut -d ':' -f1);
						if [ \"\$line\" != '' ]; then
							sudo sed -i \"\$line d\" /etc/aliases; sudo newaliases;
						fi");
				}
			}

			// 先檢查一下，到底密碼有沒有設定，且密碼設定是否合於規範？若 OK 的話，就直接修改密碼囉！
			if ( $password1 != "" ) {
				if ( strlen($password1) < 6 ) {
					$mystate = "false";
					$jhong_log = $jhong_log . "密碼長度不對！<br />";
				} elseif ( $password1 != $password2 ) {
					$mystate = "false";
					$jhong_log = $jhong_log . "輸入的兩次密碼並不相同！<br />";
				} else {
					// 要改密碼囉！
					$log = exec ("echo $password1 |  sudo passwd --stdin $username" );
					if ( $samba == 'yes' ) {
						$log = exec ( "echo  -e '$password1\n$password1\n'  | sudo pdbedit -a $username -t" );
					}
					if ( $mail == 'yes' ) {
						$log = exec ( "echo $password1 | sudo passwd --stdin mail_$username" );
					}
					$mypw = "yes";
				}
			}

			if ( $mystate != "ok" ) {
				echo "<script>alert('$jhong_log');location.href='$web/account/user_mod.php?username=$username';</script>";
				die;
			}

			if ( $needpw == "yes" && $mypw != "yes" ) echo "<script>alert('因為有改變過帳號的資訊，請記得修改密碼！')</script>";
			echo "<script>alert('修改完畢，重新載入帳號資訊！');location.href='$web/account/user_mod.php?username=$username';</script>";
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux 系統 - 實體帳號參數修訂</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <?php if ( $jhong_log != "")  echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button><span>$jhong_log</span></div>"; ?>
            <form class="form-horizontal" method="post" name="modifyuserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">修改 <?php echo $username ?> 相關參數</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-lg-2">帳號名稱：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php echo $username; ?>" disabled/>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">這個項目不可以修改！</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">使用者密碼：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password1" id="password1"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static text-danger">*若不輸入則不會修改使用者密碼喔！</span><br/>
                                <span class="form-control-static">若需要修改，密碼至少需要 6~30 個字元之間的長度</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">再輸入密碼：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password2" id="password2"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static text-danger">*若要修改密碼，則再輸入一次密碼</span><br/>
                                <span class="form-control-static text-danger">且所有此使用者的服務之密碼均會同時修改！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">真實姓名：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="name" id="name" value='<?php echo $raw_name; ?>'/>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">最多僅支援 60 個英文或 30 個中文內的長度限制</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">主要群組支援：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value='<?php echo $raw_groupname;  ?>' disabled/>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">這個項目不可以修改！</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">取消次要群組：</label>
                            <div class="col-lg-4">
                            <?php   // 找出已經加入的群組
                                for ( $j=1; $j<=$groupnumber; $j++ ) {
                                //if ( $my_groupname[$j] == $my_user_groupname[$i] ) echo $my_groupname[$j] . ", ";
                                    foreach ( $my_group_users[$j] as $grouptemp ) {
                                        if ( $grouptemp == $username ) {
                                            echo "<label class='checkbox-inline'><input type='checkbox' name='oldgroup[]' checked='checked' value='$my_groupname[$j]'/>$my_groupname[$j]</label>";
                                        }
                                    }
                                }
                            ?>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">若想要取消次要群組的支援，那就取消勾選即可</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">增加群組支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="groups" id="groups">
                                    <option value="no">不加入其他群組</option>
                                    <?php   // 找出沒有加入的群組，同時也不能是原生群組喔！
                                        for ( $j=1; $j<=$groupnumber; $j++ ) {
                                            $ingroup = "no";
                                            if ( $my_groupname[$j] == $raw_groupname ) $ingroup = "yes";
                                            foreach ( $my_group_users[$j] as $grouptemp ) {
                                                if ( $grouptemp == $username ) $ingroup = "yes";
                                            }
                                            if ( $ingroup == "no" ) echo "<option value='$my_groupname[$j]'>$my_groupname[$j]</option>";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">一次只能新增一個群組而已！若不需要則不要選擇。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">系統登入支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="shell" id="shell">
                                    <option value="no"  <?php if ($raw_shell == "no")  echo "selected='selected'"; ?>>單純服務帳號不登入</option>
                                    <option value="yes" <?php if ($raw_shell == "yes") echo "selected='selected'"; ?>>可登入系統的帳號</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">特殊功能，使用者是否可以透過 ssh 登入系統的設定項目</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">Samba 支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="samba" id="samba">
                                    <option value="yes" <?php if ($raw_smb == "yes") echo "selected='selected'"; ?>>具有/新增 Samba 支援</option>
                                    <option value="no"  <?php if ($raw_smb == "no")  echo "selected='selected'"; ?>>不具/取消 Samba 支援</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">若使用者不使用 Samba 時，可不建立此項支援！</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">郵件備份支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="mail" id="mail">
                                    <option value="yes" <?php if ($raw_mail == "yes") echo "selected='selected'"; ?>>具有/建立郵件備份帳號</option>
                                    <option value="no"  <?php if ($raw_mail == "no")  echo "selected='selected'"; ?>>不具/取消郵件備份帳號</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">若原本有支援，但選擇取消，則支援的帳號會被刪除喔！要確認妥當！</p>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <div class="col-lg-offset-5 col-lg-2">
                                <input type="hidden" name="action" value="yes" />
                                <button  type="submit" class="btn btn-primary btn-block">送出</button>
                            </div>
                        </div>
                    </div>    
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function nothing() {
		return false;
	}
	function check(f) {
		var flag=true;
		if ( f.password1.value.length > 0 || f.password2.value.length > 0 ) {
			if ( f.password1.value.length < 6 ) {
				alert("密碼長度必須要有 6 個以上字元才行！");
				f.password1.focus();
				flag=false;
				return flag;
			}
			if ( f.password1.value != f.password2.value ) {
				alert("兩個密碼的內容並不相同啊！請再次確認");
				f.password2.focus();
				flag=false;
				return flag;
			}
		}
		if ( f.name.value.length > 60 ) {
			alert("真實姓名/暱稱，應該不要超過 60 個字元吧！請再確認一次！");
			f.name.focus();
			flag=false;
			return flag;
		}
		return confirm("是否確定上傳上述資料？");
	}
</script>

<?php   include("../include/footer.php"); ?>
