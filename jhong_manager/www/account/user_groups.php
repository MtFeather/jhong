<?php	
	$buttom		= "account";
	$check_admin 	= "admin";
	$jhtitle     	= "修改群組相關資訊";
	include("../include/header.php");

	// 這個是第一部份，與新增群組有關
        if ( isset ( $_REQUEST['gotodo'] ) ) {
                $gotodo = "yes";
        } else {
                $gotodo = "no";
        }

        if ( $gotodo == "yes" ) {
	 	$group = $_REQUEST['group'];
		$groupcheck = exec ( "sudo cat /etc/group | cut -d ':' -f1 | grep '^${group}$' &> /dev/null && echo 'ok' || echo 'non' ");

 		if ( $group == "" ) {
                        echo "<script>alert('沒輸入群組名稱')</script>";
                } elseif (preg_match('/[^a-zA-Z0-9_-]/',$group)) {
                        echo "<script>alert('群組只能是數字英文字母及「_」「-」等，不能輸入其他字完！')</script>";
                } elseif ( $groupcheck == "ok" ) {
                        echo "<script>alert('此群組已經存在')</script>";
                } else {
                        echo "<script>alert('OKOK!通過檢查準備建立群組')</script>";
                        $log = exec ("sudo groupadd $group");
                }
	}

	// 這個是第二部份，與刪除群組有關
        if ( isset ( $_REQUEST['delgroup'] ) ) {
                $delgroup = $_REQUEST['delgroup'];
		if ( $delgroup == "yes" ) {
			$groupname = $_REQUEST['groupname']; 
			if ( $groupname != "usergroup" ) {
				$log = exec ( "sudo groupdel $groupname &> /dev/null && echo '成功' || echo '失敗' ");
				echo "<script>alert('刪除情況為： $log !');</script>";
			} else {
				echo "這個群組無法刪除！";
			}
		}
        }

	include("function.php");		// 載入使用者與群組的資訊
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
            <h1 class="page-header">系統防火牆觀察與設定</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-offset-2 col-lg-8">
             <form class="form-inline" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" OnSubmit="return mygroup()">
                 <div class="form-group">
                     <label>新增群組：</label>
                     <input type="hidden" name="gotodo" value="yes">
                     <input type="text" class="form-control" name="group" id="group" size="40"/>
                 </div>
                 <button type="submit" class="btn btn-default">送出</button>
            </form> 
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <br/>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>群組名稱</th>
                            <th>作為原生群組之用戶</th>
                            <th>作為次要群組之用戶</th>
                            <th>用戶設定</th>
                            <th>刪除<br />群組</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        if ( $groupnumber != 0 ) {
                            for ( $i=1; $i<=$groupnumber; $i++ ) {
                                echo "<tr><td>$my_groupname[$i]</td><td>";
                                $primary=0;
                                for ( $j=1; $j<=$usernumber; $j++) {
                                    if ( $my_user_groupname[$j] == $my_groupname[$i] ) {
                                        $primary = $primary + 1;
                                        echo $my_username[$j] . ", ";
                                    }
                                }
                                echo "</td><td>";
                                if ( count($my_group_users[$i]) > 0 ) {
                                    foreach ( $my_group_users[$i] as $grouptemp ) {
                                        echo $grouptemp . ", ";
                                    }
                                }
                                echo "</b></td><td><a href='user_groupadd.php?group=$my_groupname[$i]'><button type='submit' class='btn btn-primary btn-sm'>管理用戶</button></a></td>";
                                if ( $primary != 0 or count($my_group_users[$i]) > 0 ) {
                                    echo "<td>刪除</td>";
                                } else {
                                    echo "<td><a href='?delgroup=yes&groupname=$my_groupname[$i]'><button type='submit' class='btn btn-danger btn-sm' OnClick='return checkdelgroup()'>刪除</button></a></td>";
                                }
                                echo "</tr>";
                            }
                        }
                        //$www=shell_exec("cd $sdir/account; sh $sdir/account/array_group.sh 2>&1 ");
                        //echo  "$www";
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
            <div class="well">
                <ul class="text-danger">
                    <li>如果要刪除群組，要先把群組內的用戶先移除才可以刪除群組。</li>
                    <li>若無法刪除群組，代表該群組尚有某些用戶作為原生群組。</li>
                    <li>此時得要先將該用戶刪除後，方可刪除此群組。</li>
                </ul>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function mygroup(){
		var msg = confirm("你確定要新增群組嗎?\n\n請確認!");
		return msg;
	}
	function checkdelgroup(){
		var msg2 = confirm("你確定要 '刪除' 群組嗎?\n\n請確認!");
		return msg2;
	}
</script>
<?php   include("../include/footer.php"); ?>
