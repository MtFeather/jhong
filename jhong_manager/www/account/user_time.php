<?php	
	$buttom		= "account";
	$check_admin 	= "admin";
	$jhtitle     	= "修改使用者相關資訊";
	include("../include/header.php");

        if ( isset ( $_REQUEST['username'] ) ) {
		$usernamego = $_REQUEST['username'];
		$checkuser = "yes";
        } else {
		$checkuser = "no";
        }

	if ( isset ( $_REQUEST['action'] ) ) {
		$action     = $_REQUEST['action'];
		$usernamego = $_REQUEST['username'];
		if ( $action == "unbk" ) {
			// 解除凍結
			$log = exec ( "sudo usermod -l  $usernamego bk_$usernamego; sudo  usermod -U $usernamego 2>&1 " );

		} elseif ( $action == "bk" ) {
			// 開始凍結
			$log = exec ( "sudo usermod -l  bk_$usernamego $usernamego; sudo usermod -L bk_$usernamego 2>&1" );

		}
	}

	include ("function.php");
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
            <h1 class="page-header">系統服務狀態與系統開關機</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>帳號名稱</th>
                            <th>真實姓名</th>
                            <th>狀態</th>
                            <th>群組</th>
                            <th>家目錄</th>
                            <th>網芳<br />支援</th>
                            <th>email<br />備份</th>
                            <th>最後修改密碼</th>
                            <th colspan="3">設定</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ( $usernumber != 0 ) {
                                for ( $i=1; $i<=$usernumber; $i++ ) {
                                    if ( $my_user_bk[$i] == "yes" ) echo "<tr class='warning'>"; else echo "<tr>";
                                    echo "<td>$my_username[$i]</td>";
                                    echo "<td>$my_user_gecos[$i]</td>";
                                    if ( $my_user_bk[$i] == "yes" ) {
                                        $thisusername = "bk_$my_username[$i]";
                                        echo "<td>凍結</td>";
                                    } else {
                                        $thisusername = "$my_username[$i]";
                                        echo "<td>正常</td>";
                                    }
                                    echo "<td>";
                                    for ( $j=1; $j<=$groupnumber; $j++ ) {
                                        if ( $my_groupname[$j] == $my_user_groupname[$i] ) echo $my_groupname[$j] . ", ";
                                        foreach ( $my_group_users[$j] as $grouptemp ) {
                                            if ( $grouptemp == $my_username[$i] ) echo $my_groupname[$j] . ", ";
                                        }
                                    }
                                    echo "</td><td>";
                                    echo shell_exec( "[ -d $my_user_home[$i] ] && echo \"<span class='fa fa-check'></span>\" || echo \"<span class='fa fa-times'></span>\" ");
                                    echo "</td>";
                                    if ( $my_user_smb[$i] == "yes" ) {
                                        echo "<td><span class='fa fa-check'></span></td>";
                                    } else {
                                        echo "<td><span class='fa fa-times'></span></td>";
                                    }
                                    if ( $my_user_mail[$i] == "yes" ) {
                                        echo "<td><span class='fa fa-check'></span></td>";
                                    } else {
                                        echo "<td><span class='fa fa-times'></span></td>";
                                    }
                                    echo "<td>";
                                    echo shell_exec( "sudo LANG=C chage -l $thisusername | grep Last | cut -d ':' -f 2 2>&1 ");
                                    echo "</td>";
                                    if ( $my_user_bk[$i] == "yes" ) {
                                        echo "<td><button type='submit' name='smb' class='btn btn-success btn-sm disabled'>修改</button></td>";
                                        echo "<td><button type='submit'  class='btn btn-danger btn-sm disabled'>刪除</button></td>";
                                        echo "<td><a href='?username=$my_username[$i]&action=unbk'><button type='submit' class='btn btn-info btn-sm' Onclick='return checkunbk()'>解凍</button></a></td>";
                                    } else {
                                        echo "<td><a href='user_mod.php?username=$my_username[$i]'><button type='submit' name='smb' class='btn btn-success btn-sm'>修改</button></a></td>";
                                        echo "<td><a href='user_del.php?username=$my_username[$i]'><button type='submit'  class='btn btn-danger btn-sm'>刪除</button></a></td>";
                                        echo "<td><a href='?username=$my_username[$i]&action=bk'><button type='submit' class='btn btn-info btn-sm' Onclick='return checkbk()'>凍結</button></a></td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
            <div class="well">
                <ul>
                    <li>『凍結』狀態，表示該帳號目前『所有的服務』均不可使用之意，故凍結帳號須考慮清楚。此外，凍結亦即是『暫停使用帳號』之意；</li>
                    <li>『刪除』按鈕，尚可選擇不同的刪除方式，點選可進階查看；</li>
                    <li>『網芳支援』表示支援檔案伺服器，亦即支援 Samba 服務<；/li>
                    <li>『email備份』指該用戶具有郵件備份功能；</li>
                    <li>『最後修改密碼』為該用戶最近一次修改密碼的時間；</li>
                    <li>『修改』可以修改大部份的用戶資訊，可點選查詢。</li>
                </ul>
            </div>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->	
<script>
	function checkunbk() {
		var $msg = confirm("是否確定要解除凍結？");
		return $msg
	}
	function checkbk() {
		var $msg2 = confirm("是否確定要凍結？");
		return $msg2
	}

</script>
<?php   include("../include/footer.php"); ?>
