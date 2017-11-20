<?php 
	$buttom      = "new";
	$check_admin = "user";
	$jhtitle     = "我的帳號資訊";
	include ( "../include/header.php" );

	if ( isset ( $_REQUEST['action'] )) {
		if ( $_REQUEST['action'] == "yes" ) {
			$password1 = $_REQUEST['password1'];
			$password2 = $_REQUEST['password2'];
			$all_name  = $_REQUEST['all_name'];
			$email     = $_REQUEST['email'];
			$u_group   = $_REQUEST['u_group'];
			$tel       = $_REQUEST['tel'];

			// 先防呆！因為 email 不可以重複！
			$checksql = "select count(email) from j_user where email = '$email' and username != '$sql_username' ";
			$checkres = mysql_query($checksql,$link);
			$checkrow = mysql_fetch_row($checkres);
			if ( $checkrow[0] >= 1 ) {
				echo "此 email '$email' 已經存在系統中 $checkrow[0] 個了！無法修改！";
				echo "<script>alert('更新失敗！email 重複');location.href='$web/sql/myprofile.php';</script>";
				die;
			}

			if ( $_SESSION['userlevel'] == "admin" ) $note  = $_REQUEST['note'];

			$upsql = "update j_user set all_name='$all_name', email='$email', u_group='$u_group', tel='$tel' ";
			if ( isset ( $note ) ) $upsql = $upsql . ", note='$note' ";
			if ( $password1 != "" && $password1 == $password2 ) {
				$passwordme = sha1($password1);
				$upsql = $upsql . ", password='$passwordme' ";
			}

			$upsql = $upsql . " where username = '$sql_username'";

			if (mysql_query($upsql,$link)) {
				echo "<script>alert('更新成功囉！');location.href='$web/sql/myprofile.php';</script>";
			} else {
				echo "<script>alert('更新失敗！請向系統管理員詢問～！');location.href='$web/sql/myprofile.php';</script>";
			}
		}
	}

	$sql = "select username,all_name,level,email,u_group,tel,note,jointime from j_user where username = '$sql_username' ";
	$result = mysql_query($sql,$link);
	$row = mysql_fetch_row($result);


?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">個人帳號資訊觀察與修改</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <form  class="form-horizontal" method="post" action="myprofile.php" OnSubmit="return checkme(this)">
                        <div class="form-group">
                            <label class="control-label col-lg-2">登入帳號：</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?php echo $row[0]; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">密碼修改：</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password1" placeholder="新密碼(至少 6 個字元以上)"/>
                            </div>
                            <div class="col-lg-6">
                                 <p class="form-control-static">：新密碼(至少 6 個字元以上)</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">(若不修改則不要填)</label>
                            <div class="col-lg-4">
                                <input type="password" class="form-control" name="password2" placeholder="重複輸入一次新密碼"/>
                            </div>
                            <div class="col-lg-6">
                                 <p class="form-control-static">：重複輸入一次新密碼</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">真實姓名：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="all_name" value="<?php echo $row[1]; ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">管理等級：</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?php if ( $row[2] == 1 ) echo "管理員"; else echo "一般用戶"; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">用戶 email：</label>
                            <div class="col-lg-4">
                                <input type="email" class="form-control" name="email" value="<?php echo $row[3]; ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">所在單位：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="u_group" value="<?php echo $row[4]; ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">電話/手機：</label>
                            <div class="col-lg-4">
                                <input type="tel" class="form-control" name="tel" value="<?php echo $row[5]; ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">備註：</label>
                            <div class="col-lg-4">
                            <?php if ( $_SESSION['userlevel'] == "admin" ) { ?>
                                <input type="text" class="form-control" name="note" value="<?php echo $row[6]; ?>"/>
                            <?php } else { ?>
                                <p class="form-control-static"><?php echo $row[6]; ?></p>
                            <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">加入時間：</label>
                            <div class="col-lg-10">
                                <p class="form-control-static"><?php echo $row[7]; ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="action" value="yes" />
                            <div class="col-lg-offset-4 col-lg-2">
                                <button type="submit" class="btn btn-primary btn-block">更改送出</button>
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
		if ( f.password1.value != "" ) {
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
		}
                return confirm("是否確定修改上述資料？");
	}
</script>

<?php   include( "../include/footer.php" ); ?>


