<?php 
	$buttom      = "samba_server";
	$check_admin = "admin";
	$jhtitle     = "修改網芳的分享目錄";
	include ( "../include/header.php" );
	include ( "../account/function.php" );
	include ( "./samba_function.php" );

	if ( ! isset ( $_REQUEST['sharename'] ) ) {
		echo "<script>alert('沒有輸入任何分享的資原名稱，無法執行此項目。'); location.href='$web/samba/samba_show.php';</script>";
	} else {
		$sharename = $_REQUEST['sharename'];
		if ( $smbnumber > 0 ) {
			for ( $i=1; $i<=$smbnumber; $i++ ) {
				if ( $smb_sharename[$i] == $sharename ) {
					$thisline = $i;
					break;
				}
			}
		}
	}

	if ( $smb_writable[$thisline] == "yes" ) {
		$wyes = "selected='selected'"; $wno = "";
	} else {
		$wno = "selected='selected'"; $wyes = "";
	}
/*
        $smbnumber                              // Samba 分享的資源總數量
        $smb_filename[$smbnumber]               // 每個獨立的設定檔所在絕對路徑
        $smb_sharename[$smbnumber]              // 分享的名稱
        $smb_dirname[$smbnumber]                // 所在目錄絕對路徑
        $smb_writable[$smbnumber]               // yes/no
        $smb_perm_group[$smbnumber][$gnu]       // 可操作此目錄的群組
        $smb_perm_user[$smbnumber][$gnu]        // 可操作此目錄的用戶

*/
	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$writable  = $_REQUEST['writable'];
			$groups    = $_REQUEST['groups'];
			$users     = $_REQUEST['users'];
			/*echo "$sharename|$writable|";
			foreach ( $groups as $temp ) echo "$temp|";
			foreach ( $users as $temp ) echo "$temp|";
			echo "<br />";*/

			$myerror = "yes";
			foreach ( $groups as $temp ) { if ( $temp != "no" ) $myerror = "no"; }
			foreach ( $users  as $temp ) { if ( $temp != "no" ) $myerror = "no"; }

			if ( $myerror == "yes" ) {
				echo "<script>alert('沒有選擇任何群組或用戶'); location.href='$web/samba/samba_mod.php?sharename=$sharename';</script>";
			}

			// 加入群組與使用者的相關 ACL 權限
			$mydirname = "/jhong/samba/${sharename}";
			$log = exec ( "sudo setfacl -b $mydirname" );
			foreach ( $groups as $temp ) {
				$log = exec ( "sudo setfacl -m g:${temp}:rwx $mydirname ; sudo setfacl -m d:g:${temp}:rwx $mydirname " );
			}
			foreach ( $users as $temp ) {
				$log = exec ( "sudo setfacl -m u:${temp}:rwx $mydirname ; sudo setfacl -m d:u:${temp}:rwx $mydirname " );
			}

			// 取得群組以及用戶的組合
			$allgroup = "";
			foreach ( $groups as $temp ) {
				if ( $temp != "no" ) {
					if ( $allgroup == "" ) $allgroup = "@$temp"; else $allgroup = "${allgroup},@$temp";
				}
			}
			$alluser = "";
			foreach ( $users as $temp ) {
				if ( $temp != "no" ) {
					if ( $alluser == "" ) $alluser = $temp; else $alluser = "${alluser},$temp";
				}
			}
			
			$allgroupuser = "";
			if ( $allgroup != "" ) $allgroupuser = $allgroup;
			if ( $alluser  != "" ) {
				if ( $allgroupuser != "" ) $allgroupuser = "${allgroupuser},$alluser"; else $allgroupuser = $alluser;
			}

			// 開始編輯設定檔了！
			$myfilename = "/etc/samba/jhong/${sharename}";
			$log = shell_exec ( "sudo bash -c \"echo '[${sharename}]
	comment  = ${sharename}
	path     = ${mydirname}
	writable = ${writable}
	browseable = yes
	valid users = ${allgroupuser}' > $myfilename \" " );

			// 最終就得要重新啟動 samba 囉！
			$log = exec ( "sudo systemctl restart smb nmb" );
			echo "<script>alert('修改完畢喔！'); location.href='$web/samba/samba_mod.php?sharename=$sharename';</script>";
			die;
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux  - 修改分享的資源與目錄</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" name="addsambaform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">修改網芳分享的資源</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-lg-2">分享的名稱：</label>
                            <div class="col-lg-2">
                                <p class="form-control-static"><?php echo $smb_sharename[$thisline] ?></p>
                            </div>
                            <div class="col-lg-8">
                                <p class="form-control-static text-danger">這個項目不可修改！</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">讀/寫狀態：</label>
                            <div class="col-lg-2">
                                <select name="writable" class="form-control">
                                    <option value="yes" <?php echo $wyes; ?>>可寫入</option>
                                    <option value="no" <?php echo $wno; ?>>唯讀</option>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <span class="form-control-static">要注意，如果設定為可寫入，若使用者對於目錄無權限，將還是無法寫入。</span><br/>
                                <span class="form-control-static">若設定為唯讀，則即使用戶有寫入權限，依舊無法寫入！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">可登入群組選擇：</label>
                            <div class="col-lg-2">
                                <?php
                                    foreach ( $smb_perm_group[$thisline] as $temp )
                                        echo "<div class='checkbox'><label><input type='checkbox' name='groups[]' checked='checked' value='$temp' />$temp</label></div>";
                                ?>
                                <select name="groups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) {
                                            $myans = "yes";
                                            foreach ( $smb_perm_group[$thisline] as $temp2 ) {
                                                if ( $temp2 == $temp ) $myans = "no";
                                            }
                                           if ( $myans == "yes" ) echo "<option value='$temp'>$temp</option>\n";
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <span class="form-control-static">本目錄允許操作的群組選擇。</span><br/>
                                <span class="form-control-static">如果需要製作讓很多用戶操作的目錄，建議可以這樣做：</span><br/>
                                <span class="form-control-static text-danger">1. 建置一個共享群組； 2. 讓用戶加入此群組； 3. 在這個項目選擇群組的支援。</span>
                                <span class="form-control-static">這樣就可以讓很多用戶同時操作，而不須一個一個用戶加入此資源的分享。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">可登入用戶選擇：</label>
                            <div class="col-lg-2">
                                <?php
                                    foreach ( $smb_perm_user[$thisline] as $temp )
                                        echo "<div class='checkbox'><label><input type='checkbox' name='users[]' checked='checked' value='$temp' />$temp</label></div>";
                                ?>
                                <select name="users[]" class="form-control">
                                    <option value="no">不須用戶支援</option>
                                    <?php   // 取得群組！
                                        for ( $i=1; $i<=$usernumber; $i++ ) {
                                            if ( $my_user_bk[$i] == "no" && $my_user_smb[$i] == "yes" ) {
                                                $temp = $my_username[$i];
                                                $myans = "yes";
                                                foreach ( $smb_perm_user[$thisline] as $temp2 ) {
                                                    if ( $temp2 == $temp ) $myans = "no";
                                                }
                                                if ( $myans == "yes" ) echo "<option value='$temp'>$temp</option>\n";
                                            }
                                        }

                                       /*foreach ( $my_username as $temp ) {
                                           $myans = "yes";
                                           foreach ( $smb_perm_user[$thisline] as $temp2 ) {
                                               if ( $temp2 == $temp ) $myans = "no";
                                           }
                                           if ( $myans == "yes" ) echo "<option value='$temp'>$temp</option>\n";
                                       }*/
                                   ?>
                                </select>
                            </div>
                            <div class="col-lg-8">
                                <p class="form-control-static">請一個一個慢慢建立囉！</p>
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class="col-lg-offset-5 col-lg-2">
                                <input type="hidden" name="action" value="yes" />
                                <button type="submit" class='btn btn-primary btn-block'>開始修改喔！</button>
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
<?php   include( "../include/footer.php" ); ?>
