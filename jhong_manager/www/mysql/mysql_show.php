<?php	
	$buttom		= "sql";
	$check_admin 	= "admin";
	$jhtitle     	= "SQL 資料庫與帳號列表與管理";
	include("../include/header.php");
	include("./mysql_config.php");
	include("./mysql_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "dataadd" ) {
			$database  = $_REQUEST['database'];
			$dataadmin = $_REQUEST['dataadmin'];

			// 先防呆！
			$tempres = mysql_query ( "show databases", $link2);
			while ( $temprow = mysql_fetch_row($tempres) ) {
				if ( $temprow[0] == $database ) {
					echo "<script>alert('這個資料庫已經存在！請重新建立其他資料庫名稱'); location.href='$web/mysql/mysql_show.php';</script>";
					die;
				}
			}

			$sqlres = "create database $database";
			$logres = mysql_query($sqlres,$link2);
			if ( $dataadmin != "no" ) {
				$sqlres = "grant all privileges on $database.* to '$dataadmin'@'localhost' ";
				$logres = mysql_query($sqlres,$link2);
			}
			$tempres = mysql_query ( "flush privileges", $link2);
                       	echo "<script>alert('建立完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
                       	die;
		}

		if ( $_REQUEST['action'] == "deldata" ) {
			$database  = $_REQUEST['database'];

			$checkdata = checkdatabasename($database);
			if ( $checkdata == 'OK' ) {
				$sqlres = "delete from db where Db = '$database' ";
				$logres = mysql_query($sqlres,$link2);

				$sqlres = "drop database $database ";
				$logres = mysql_query($sqlres,$link2);

				$tempres = mysql_query ( "flush privileges", $link2);
                       		echo "<script>alert('刪除用戶完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
                       		die;
			}
		}
	}

	$databasesql = "show databases";
	$databaseresult = mysql_query($databasesql,$link2);
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
            <h1 class="page-header">SQL 資料庫列表與管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-inline text-center" method="post" action="mysql_show.php" OnSubmit="return checkdata(this)">
                <div class="form-group">
                    <label>新增資料庫：</label>
                    <input type="text" class="form-control" name="database"/>
                </div>
                <div class="form-group">
                    <label>資料管理員：</label>
                    <select name="dataadmin" class="form-control">
                        <option value="no">不指定管理員</option>
                        <?php
                            $usersql = "select User from user where $sysuser and Host = 'localhost' order by User";
                            $userresult = mysql_query($usersql,$link2);
                            while ( $userrow = mysql_fetch_row($userresult ) )  {
                                echo "<option value='$userrow[0]'>$userrow[0]</option>";
                            }
                        ?>
                    </select>
                </div>
                <input type="hidden" name="action" value="dataadd" />
                <button type="submit" class="btn btn-primary">新增資料庫</button>
            </form>
            <br/>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>現有的資料庫名稱</th>
                            <th>資料庫管理員名單</th>
                            <th>管理</th>
                        </tr>
                    </thead>
                    <tbody>
	                <?php 	
                            while ( $row = mysql_fetch_row ($databaseresult) )  {
                                $checkdata = checkdatabasename($row[0]);
                                if ( $checkdata == 'OK' ) {
                                    echo "<tr><td>$row[0]</td><td>";
                                    $tempsql = "select User from db where Db = '$row[0]' order by User"; 
                                    $tempres = mysql_query($tempsql, $link2 );
                                    while ( $temprow = mysql_fetch_row($tempres) ) {
                                        echo "$temprow[0], ";
                                    }
                                    echo "</td><td><a href='mysql_show.php?database=$row[0]&action=deldata' class='btn btn-danger' Onclick='return checkdeldata()'>刪除</a> ";
                                    echo "<a href='mysql_addadm.php?database=$row[0]' class='btn btn-primary' Onclick='return checkaddadm()'>管理/匯進/匯出</a> ";
                                }
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
        function checkdata(f) {
                var re = /^[A-Za-z][A-Za-z0-9_-]+$/;
                if (!re.test(f.database.value)) {
                        alert("你的資料庫名稱開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.database.focus();
                        return false;
                }

                if ( f.database.value.length < 3 ) {
                        alert("資料庫名稱長度小於 3 個字元了！！");
                        f.database.focus();
                        return false;
                }

                return confirm("是否確定新增上述資料？");
        }

	function checkdeldata() {
		return confirm("是否確定要刪除此資料庫？");
	}

</script>

<?php   include("../include/footer.php"); ?>
