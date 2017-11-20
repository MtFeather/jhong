<?php	
	$buttom		= "sql";
	$check_admin 	= "admin";
	$jhtitle     	= "SQL 資料庫與帳號列表與管理";
	include("../include/header.php");
	include("./mysql_config.php");
	include("./mysql_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "useradd" ) {
			$datauser  = $_REQUEST['datauser'];
			$password1 = $_REQUEST['password1'];
			$password2 = $_REQUEST['password2'];

			// 先防呆！
			$tempres = mysql_fetch_row(mysql_query ( "select count(User) from user where User = '$datauser'", $link2));
			if ( $tempres[0] > 0 ) {
				echo "<script>alert('這個帳號已經存在！請重新使用其他帳號建立！'); location.href='$web/mysql/mysql_user.php';</script>";
				die;
			}

			if ( $password1 != "" && $password1 == $password2 ) {
				$sqlres = "CREATE USER '$datauser'@'localhost' IDENTIFIED BY '$password1'";
				$logres = mysql_query($sqlres,$link2);
				$tempres = mysql_query ( "flush privileges", $link2);
                        	echo "<script>alert('建立完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_user.php';</script>";
                        	die;
			} 
		}

		if ( $_REQUEST['action'] == "deluser" ) {
			$datauser  = $_REQUEST['datauser'];

			$sqlres = "delete from user where User = '$datauser' and $sysuser ";
			$logres = mysql_query($sqlres,$link2);
			$sqlres = "delete from db where User = '$datauser' and $sysuser ";
			$logres = mysql_query($sqlres,$link2);

			$tempres = mysql_query ( "flush privileges", $link2);
                       	echo "<script>alert('刪除用戶完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_user.php';</script>";
                       	die;
		}
	}

	$usersql = "select User from user where $sysuser and Host = 'localhost' order by User";
	$userresult = mysql_query($usersql,$link2);
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">SQL 資料庫用戶列表與管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-inline text-center" method="post" action="mysql_user.php" OnSubmit="return checkuser(this)">
                <div class="form-group">
                    <label>新增用戶：</label>
                    <input type="text" class="forn-control" name="datauser"/>
                </div>
                <div class="form-group">
                    <label>用戶密碼：</label>
                    <input type="password" class="forn-control" name="password1"/>
                </div>
                <div class="form-group">
                    <label>用戶密碼：</label>
                    <input type="password" class="forn-control" name="password2"/>
                </div>
		<input type="hidden" name="action" value="useradd" />
		<button type="submit" class="btn btn-primary">新增資料庫用戶</button>
            </form>
            <br/>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>現有的使用者名稱</th>
                            <th>管理的資料庫系統</th>
                            <th>管理</th>
                        </tr>
                    </thead>
                    <tbody>
	            <?php 	
                        while ( $userrow = mysql_fetch_row($userresult ) )  {
                            echo "<tr><td>$userrow[0]</td><td>";
                            $tempsql = "select Db from db where User = '$userrow[0]' order by db"; 
                            $tempres = mysql_query($tempsql, $link2 );
                            while ( $temprow = mysql_fetch_row($tempres) ) {
                                echo "$temprow[0], ";
                            }
                            echo "</td><td><a href='mysql_user.php?datauser=$userrow[0]&action=deluser' class='btn btn-danger' Onclick='return checkdeluser()'>刪除</a></td></tr>\n";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
        function checkuser(f) {
                var re = /^[A-Za-z][A-Za-z0-9_-]+$/;
                if (!re.test(f.datauser.value)) {
                        alert("你的帳號開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.datauser.focus();
                        return false;
                }

                if ( f.datauser.value.length < 3 ) {
                        alert("帳號長度小於 3 個字元了！！");
                        f.datauser.focus();
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

                return confirm("是否確定上傳上述資料？");
        }

	function checkdeluser() {
		return confirm("是否確定要刪除此用戶？");
	}
</script>

<?php   include("../include/footer.php"); ?>
