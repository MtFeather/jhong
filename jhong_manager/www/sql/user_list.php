<?php 
	$buttom      = "new";
	$check_admin = "admin";
	$jhtitle     = "網頁用戶之帳號列表";
	include ( "../include/header.php" );

	if ( isset ( $_REQUEST['action'] )) {
		if ( $_REQUEST['action'] == "userdel" ) {
			$deluid = $_REQUEST['uid'];

			$delsql = " delete from j_user where uid = $deluid and ( username != 'admin' or username != 'jhongadmin' ) ";
			$result = mysql_query($delsql,$link);

			echo "<script>location.href='$web/sql/user_list.php';</script>";
		}
	}

	$sql = "select username,all_name,level,email,u_group,tel,note,jointime,uid from j_user where username != 'jhongadmin' order by username";
	$result = mysql_query($sql,$link);
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">網頁用戶帳號列表</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>帳號名稱</th>
                            <th>真實姓名</th>
                            <th>等級類別</th>
                            <th>用戶 email</th>
                            <th>所屬團隊</th>
                            <th>管理動作</th>
                        </tr>
                    </thead>
                    <tbody>
<?php 	
	while($row = mysql_fetch_row($result))  {
		echo "<tr><td>$row[0]</td><td>$row[1]</td><td>";
		if ( $row[2] == 1 ) echo "管理員"; else echo "一般用戶";
		echo "</td><td>$row[3]</td><td>$row[4]</td><td>";
		if ( $row[0] != 'admin' && $row[0] != 'jhongadmin' ) {
			echo "<a href='user_list.php?action=userdel&uid=$row[8]' class='btn btn-primary btn-sm' OnClick='return checkme()'>刪除</a> ";
			echo "<a href='user_mod.php?uid=$row[8]' class='btn btn-primary btn-sm'>管理</a> ";
		} else {
			echo "--";
		}
		echo "</td></tr>\n";
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
	function checkme() {
		return confirm("確定刪除此帳號嘛？");
	}
</script>

<?php   include( "../include/footer.php" ); ?>
