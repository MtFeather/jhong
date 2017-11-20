<?php
	$buttom		= "account";
	$check_admin 	= "admin";
        $jhtitle     	= "帳號建置";
	include("../include/header.php");
	include("function.php");

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$username  = $_REQUEST['username'];
			$password1 = $_REQUEST['password1'];
			$password2 = $_REQUEST['password2'];
			$name      = $_REQUEST['name'];
			if ( $name == "" ) $option_name = "-c '$username'"; else $option_name = "-c '$name'";
			$group     = $_REQUEST['group'];
			if ( $group == "no" ) $group = $username;
			$groups    = $_REQUEST['groups'];
			if ( $groups == "no" ) $option_groups = "" ; else $option_groups = "-G $groups";
			$shell     = $_REQUEST['shell'];
			if ( $shell == "no" ) $option_shell = "-s /sbin/nologin"; else $option_shell = "";
			$samba     = $_REQUEST['samba'];
			$mail      = $_REQUEST['mail'];
			$mystate   = "ok";
			$jhong_log = "";

			// 先檢查帳號資訊
			foreach ( $my_username as $tempusername ) {
				if ( $tempusername == $username ) {
					$mystate = "false";
					$jhong_log = $jhong_log . "此帳號已經存在系統中<br />";
				}
			}
			if ( strlen($username) < 1 || strlen($username) > 30 ) {
				$mystate = "false";
				$jhong_log = $jhong_log . "帳號長度不對！<br />";
			} elseif ( preg_match ('/[^.a-zA-Z0-9_-]/', $username ) ) {
				$mystate = "false";
				$jhong_log = $jhong_log . "帳號只能是數字英文字母及「_」「-」等，不能輸入其他字元！<br />";
			}

			// 再來檢查密碼
			if ( strlen($password1) < 6 ) {
				$mystate = "false";
				$jhong_log = $jhong_log . "密碼長度不對！<br />";
			} elseif ( $password1 != $password2 ) {
				$mystate = "false";
				$jhong_log = $jhong_log . "輸入的兩次密碼並不相同！<br />";
			}

			// 檢查主要群組的支援情況
			$logtemp = exec ( "[ \"$(grep '^${group}:' /etc/group)\" != '' ] && echo exist || echo non-exist" );
			if ( $logtemp == "exist" ) $option_group = "-g $group"; else $option_group ="";

			// 檢查如果成功，那就開始建置！建置完並且需要回到帳號列表處！
			if ( $mystate == "ok" ) {
				//echo "sudo useradd $option_group $option_groups $option_name $option_shell $username" . "<br />";
				//echo "echo $password1 |  sudo passwd --stdin $username" . "<br />";
				$checkuserhome = shell_exec ( "[ -d /home/${username} ] && echo 'exist'" );
				$checkuserhome = trim(str_replace ("\r\n","",$checkuserhome));
				$log = exec ("sudo useradd $option_group $option_groups $option_name $option_shell $username");
				$log = exec ("echo $password1 |  sudo passwd --stdin $username" );
				if ( $checkuserhome == "exist" && $username != "" ) {
					$log = exec ( "sudo chown -R '$username' /home/${username} " );
					$log = exec ( "sudo chown    '$username' /var/spool/mail/${username} " );
				}
				//if ( 
				if ( $samba == 'yes' ) {
					//echo "echo  -e '$password1\n$password1\n'  | sudo pdbedit -a $username -t" . "<br />";
					$log = exec ( "echo  -e '$password1\n$password1\n'  | sudo pdbedit -a $username -t" );
				}
				if ( $mail == 'yes' ) {
					// echo "sudo useradd -g backupmail -s /sbin/nologin $option_name mail_$username" . "<br />";
					// echo "echo $password1 | sudo passwd --stdin mail_$username" . "<br />";
					// echo "sudo bash -c \" echo $username: $username,mail_$username >> /etc/aliases \" ";
					$checkuserhome = shell_exec ( "[ -d /home/mail_${username} ] && echo 'exist'" );
					$checkuserhome = trim(str_replace ("\r\n","",$checkuserhome));
					$log = exec ( "sudo useradd -g backupmail -s /sbin/nologin $option_name mail_$username" );
					$log = exec ( "echo $password1 | sudo passwd --stdin mail_$username" );
					$log = exec ( "sudo bash -c \" echo $username: $username,mail_$username >> /etc/aliases ; newaliases \" " );
					if ( $checkuserhome == "exist" && $username != "" ) {
						$log = exec ( "sudo chown -R 'mail_$username' /home/mail_${username} " );
						$log = exec ( "sudo chown    'mail_$username' /var/spool/mail/mail_${username} " );
					}
				}
				echo "<script>location.href='$web/account/user_time.php';</script>";
			}
			//echo "'$username' |'$password1'| '$password2'| '$name' |'$group' |'$groups' | '$samba'| '$mail'  ";
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">使用者帳號建置</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <?php if ( $jhong_log != "")  echo "<div class='alert alert-danger alert-dismissable'><button type='button' class='close' data-dismiss='alert' aria-hidden='true'>×</button><span>$jhong_log</span></div>"; ?>
            <form class="form-horizontal" method="post" name="adduserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">新建系統帳號</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-lg-2">帳號名稱：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="username" id="username" <?php if ( isset ( $username ) ) echo "value='$username'";  ?>/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">*開頭必須是英文，帳號長度須在 5~30 個字元內</span><br/>
                                <span class="form-control-static">且只能是英文、數字、底線(_或-)及小數點的組合</span>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="control-label col-lg-2">使用者密碼：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password1" id="password1"/>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">*密碼至少需要 6~30 個字元之間的長度才行！</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">再輸入密碼：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password2" id="password2"/>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">*再輸入一次密碼</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">真實姓名：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="name" id="name" <?php if ( isset ( $name ) ) echo "value='$name'";  ?>/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">最多僅支援 60 個英文或 30 個中文內的長度限制</span><br />
                                <span class="form-control-static">若不輸入，則與帳號名稱相同！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">主要群組支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="group" id="group">
                                    <option value="usergroup">系統預設群組</option>
                                    <?php foreach ( $my_groupname as $grouptemp ) echo "<option value='$grouptemp'>$grouptemp</opton>";  ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">系統預設會建立與帳號同名的群組，您也可以選擇特定的群組，或使用次要群組支援</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">次要群組支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="groups" id="groups">
                                    <option value="no">不加入其他群組</option>
                                    <?php foreach ( $my_groupname as $grouptemp ) echo "<option value='$grouptemp'>$grouptemp</opton>"; ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">是否加入次要群組的支援(本項與網芳相關性較高)</span><br/>
                                <span class="form-control-static">若須兩個以上群組支援，請建置完成後到列表中修改</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">系統登入支援：</label>
                            <div class="col-lg-4">
                                <select class="form-control" name="shell" id="shell">
                                    <option value="no">單純服務帳號不登入</option>
                                    <option value="yes">可登入系統的帳號</option>
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
                                    <option value="yes">建立 Samba 支援</option>
                                    <option value="no">不支援</option>
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
                                    <option value="yes">建立郵件備份帳號</option>
                                    <option value="no">不支援</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">若使用者不使用 Mail 時，可不建立此項支援！</p>
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
	function check(f) {
		// 先看帳號是否正確
		var flag=true;
		var re = /^[A-Za-z][.A-Za-z0-9_-]+$/;
		if (!re.test(f.username.value)) {
			alert("你的帳號開頭必須要英文，且只能是英文、數字、底線( _ 或 - )及小數點的組合，不可輸入其他字元喔！");
			f.username.focus();
			flag=false;
			return flag;
		}
		if ( f.username.value.length < 1 || f.username.value.length > 30) {
			alert("你的帳號名稱必須要有 1 ~ 30 個字元之間！");
			f.username.focus();
			flag=false;
			return flag;
		}
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
