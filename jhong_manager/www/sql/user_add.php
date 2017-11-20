<?php 
	$buttom      = "new";
	$check_admin = "admin";
	$jhtitle     = "我的帳號資訊";
	include ( "../include/header.php" );

	if ( isset ( $_REQUEST['action'] )) {
		if ( $_REQUEST['action'] == "yes" ) {
			$username  = $_REQUEST['username'];
			$password1 = $_REQUEST['password1'];
			$password2 = $_REQUEST['password2'];
			$all_name  = $_REQUEST['all_name'];
			$level     = $_REQUEST['level'];
			$email     = $_REQUEST['email'];
			$u_group   = $_REQUEST['u_group'];
			$tel       = $_REQUEST['tel'];
			$note      = $_REQUEST['note'];

                        // 先防呆！因為 email 不可以重複！
                        $checksql = "select count(email) from j_user where email = '$email' ";
                        $checkres = mysql_query($checksql,$link);
                        $checkrow = mysql_fetch_row($checkres);
                        if ( $checkrow[0] >= 1 ) {
                                echo "此 email '$email' 已經存在系統中 $checkrow[0] 個了！無法修改！";
                                echo "<script>alert('建立失敗！email 重複');location.href='$web/sql/user_add.php';</script>";
				die;
                        }
                        $checksql = "select count(username) from j_user where username = '$username' ";
                        $checkres = mysql_query($checksql,$link);
                        $checkrow = mysql_fetch_row($checkres);
                        if ( $checkrow[0] >= 1 ) {
                                echo "此帳號 '$username' 已經存在系統中了！無法修改！";
                                echo "<script>alert('建立失敗！帳號重複');location.href='$web/sql/user_add.php';</script>";
				die;
                        }

			if ( $password1 != "" && $password1 == $password2 ) {
				$passwordme = sha1($password1);
			} else {
				echo "密碼錯誤！不能建立！";
				die;
			}

			$inssql = "insert into j_user (username, password, all_name, level, u_group, email, tel, note, jointime) value ('$username', '$passwordme', '$all_name', $level, '$u_group', '$email', '$tel', '$note', now())";

			$upresult = mysql_query($inssql,$link);

			echo "<script>location.href='$web/sql/user_list.php';</script>";
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">建立網頁界面使用帳號</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form  class="form-horizontal" method="post" action="user_add.php" OnSubmit="return checkme(this)">
                        <div class="form-group">
                            <label class="control-label col-lg-2">登入帳號：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="username"/>
                            </div>
                            <div class="col-lg-6">
                                 <p class="form-control-static">：至少需要 3 個字元</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">登入密碼：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password1" placeholder="新密碼(至少 6 個字元以上)"/>
                            </div>
                            <div class="col-lg-6">
                                 <p class="form-control-static">：新密碼(至少 6 個字元以上)</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-4">
                                <input type="password" class="form-control" name="password2" placeholder="重複輸入一次新密碼"/>
                            </div>
                            <div class="col-lg-6">
                                 <p class="form-control-static">：重複輸入一次新密碼</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">真實姓名：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="all_name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">管理等級：</label>
                            <div class="col-lg-2">
                                <select class="form-control" name="level">
                                    <option value="2">一般用戶</option>
                                    <option value="1">系統管理員</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">用戶 email：</label>
                            <div class="col-lg-4">
                                <input type="email" class="form-control" name="email"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">隸屬部門：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="u_group"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">電話/手機：</label>
                            <div class="col-lg-4">
                                <input type="tel" class="form-control" name="tel"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">備註：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="note"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="action" value="yes" />
                            <div class="col-lg-offset-4 col-lg-2">
                                <button type="submit" class="btn btn-primary btn-block">開始建立新帳號</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.form  -->
                </div>
                <!-- /.panel-body  -->
            </div>
            <!-- /.panel  -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checkme(f) {
                var re = /^[A-Za-z][A-Za-z0-9_-]+$/;
                if (!re.test(f.username.value)) {
                        alert("你的帳號開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.username.focus();
                        return false;
		}

		if ( f.username.value.length < 3 ) {
			alert("帳號長度小於 3 個字元了！！");
			f.username.focus();
			return false;
		}

		if ( f.password1.value.length < 6 ) {
			alert("密碼小於 6 個字元喔！");
			f.password1.focus();
			return false;
		}

               	if ( f.password1.value != f.password2.value ) {
               	        alert("兩個密碼的內容並不相同啊！請再次確認");
               	        f.password2.focus();
			return false;
               	}

                return confirm("是否確定修改上述資料？");
	}
</script>

<?php   include( "../include/footer.php" ); ?>


