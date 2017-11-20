<?php 
	$buttom      = "samba_server";
	$check_admin = "admin";
	$jhtitle     = "增加網芳的分享目錄";
	include ( "../include/header.php" );
	include ( "../account/function.php" );
	include ( "./samba_function.php" );

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$sharename = $_REQUEST['sharename'];
			$writable  = $_REQUEST['writable'];
			$groups    = $_REQUEST['groups'];
			$rogroups  = $_REQUEST['rogroups'];
			$users     = $_REQUEST['users'];
			/*echo "$sharename|$writable|";
			foreach ( $groups as $temp ) echo "$temp|";
			foreach ( $users as $temp ) echo "$temp|";
			echo "<br />";*/

			$mydirname = "/jhong/samba/${sharename}";
			// 視察目錄，若目錄不在，則建立他
			$log = exec ( "[ ! -d $mydirname ] && sudo mkdir -p $mydirname " );

			// 加入群組與使用者的相關 ACL 權限
			foreach ( $groups as $temp ) {
				$log = exec ( "sudo setfacl -m g:${temp}:rwx $mydirname ; sudo setfacl -m d:g:${temp}:rwx $mydirname " );
			}
			foreach ( $rogroups as $temp ) {
				$log = exec ( "sudo setfacl -m g:${temp}:r-x $mydirname ; sudo setfacl -m d:g:${temp}:r-x $mydirname " );
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
			foreach ( $rogroups as $temp ) {
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
	valid users = ${allgroupuser}' > $myfilename 
	echo 'include = $myfilename' >> /etc/samba/smb.conf \" " );

			// 最終就得要重新啟動 samba 囉！
			$log = exec ( "sudo systemctl restart smb nmb" );
			echo "<script>alert('建置完畢，請檢查列表資訊'); location.href='$web/samba/samba_show.php';</script>";
			die;
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux  - 增加分享的資源與目錄</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" name="addsambaform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">新增網芳分享的資源</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-lg-2">分享的名稱：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="sharename"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">妳想要在網路上看到的資料夾名稱為何之意。</span><br/>
                                <span class="form-control-static">系統會同時建立同名的目錄給這個資源使用。</span><br/>
                                <span class="form-control-static text-danger">這個名稱必須是唯一，且不能與任何帳號名稱相同；</span><br/>
                                <span class="form-control-static text-danger">同時這個名稱只能是英文、數字與 - _ 的組合才行，</span><br/>
                                <span class="form-control-static text-danger">最後，資源名稱必須大於 5 個字元以上才行喔！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">讀/寫狀態：</label>
                            <div class="col-lg-4">
                                <select name="writable" class="form-control">
                                    <option value="yes">可寫入</option>
                                    <option value="no">唯讀</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">要注意，如果設定為可寫入，若使用者對於目錄無權限，將還是無法寫入。</span><br/>
                                <span class="form-control-static">若設定為唯讀，則即使用戶有寫入權限，依舊無法寫入！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">可登入群組選擇 (可寫入)：</label>
                            <div class="col-lg-4">
                                <select name="groups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                                <select name="groups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                                <select name="groups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">本目錄允許操作的群組選擇。<br />如果需要製作讓很多用戶操作的目錄，建議可以這樣做：</span><br/>
                                <span class="form-control-static text-danger">1. 建置一個共享群組； 2. 讓用戶加入此群組； 3. 在這個項目選擇群組的支援。</span>
                                <span class="form-control-static">這樣就可以讓很多用戶同時操作，而不須一個一個用戶加入此資源的分享。</span><br/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">可登入群組選擇 (僅唯讀)：</label>
                            <div class="col-lg-4">
                                <select name="rogroups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                                <select name="rogroups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                                <select name="rogroups[]" class="form-control">
                                    <option value="no">不須群組支援</option>
                                    <?php   // 取得群組！
                                        foreach ( $my_groupname as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">本目錄允許操作的群組選擇。</span><br/>
                                <span class="form-control-static">如果需要製作讓很多用戶操作的目錄，建議可以這樣做：</span><br/>
                                <span class="form-control-static text-danger">1. 建置一個共享群組； 2. 讓用戶加入此群組； 3. 在這個項目選擇群組的支援。</span>
                                <span class="form-control-static">這樣就可以讓很多用戶同時操作，而不須一個一個用戶加入此資源的分享。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">可登入用戶選擇：</label>
                            <div class="col-lg-4">
                                <select name="users[]" class="form-control">
                                    <option value="no">不須用戶支援</option>
                                    <?php   // 取得群組！
                                        //foreach ( $my_username as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                       for ( $i=1; $i<=$usernumber; $i++ ) {
                                       if ( $my_user_bk[$i] == "no" && $my_user_smb[$i] == "yes" )
                                           echo "<option value='$my_username[$i]'>$my_username[$i]</option>\n";
                                       }
                                    ?>
                                </select>
                                <select name="users[]" class="form-control">
                                    <option value="no">不須用戶支援</option>
                                    <?php   // 取得群組！
                                        //foreach ( $my_username as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                       for ( $i=1; $i<=$usernumber; $i++ ) {
                                       if ( $my_user_bk[$i] == "no" && $my_user_smb[$i] == "yes" )
                                           echo "<option value='$my_username[$i]'>$my_username[$i]</option>\n";
                                       }
                                    ?>
                                </select>
                                <select name="users[]" class="form-control">
                                    <option value="no">不須用戶支援</option>
                                    <?php   // 取得群組！
                                        //foreach ( $my_username as $temp ) echo "<option value='$temp'>$temp</option>\n";
                                       for ( $i=1; $i<=$usernumber; $i++ ) {
                                       if ( $my_user_bk[$i] == "no" && $my_user_smb[$i] == "yes" )
                                           echo "<option value='$my_username[$i]'>$my_username[$i]</option>\n";
                                       }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <p class="form-control-static">建立用戶的支援狀態！預設最多支援三個人喔！</p>
                            </div>
                        </div>
                        <div class='form-group'>
                            <div class="col-lg-offset-5 col-lg-2">
                                <input type="hidden" name="action" value="yes" />
                                <button type="submit" class='btn btn-primary btn-block'>開始建立</button>
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
		// 先抓出目前的帳號以及相關的資源名稱，因為不能重複發生喔
		var existsharename = new Array();
	<?php 	for ( $i=1; $i<=$usernumber; $i++ ) { echo "existsharename[$i] = '$my_username[$i]' ;\n"; } ?>
	<?php 	for ( $i=1; $i<=$smbnumber ; $i++ ) {
			$temp = $i + $usernumber;
			echo "existsharename[$temp] = '$smb_sharename[$i]' ;\n"; } ?>
		// 還缺既有的資源名稱喔！
		var totalname = <?php echo $usernumber+$smbnumber; ?> ;

                // 先看資源名稱是否符合正確！
                var flag=true;
                var re = /^[A-Za-z0-9_-]+$/;
                if (!re.test(f.sharename.value)) {
                        alert("資源名稱只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.sharename.focus();
                        flag=false;
                        return flag;
                }
                if ( f.sharename.value.length < 5 ) {
                        alert("資源名稱名稱必須要有 5 個字元以上！");
                        f.sharename.focus();
                        flag=false;
                        return flag;
                }
		for ( i=1; i<=totalname; i++ ){
			if ( f.sharename.value == existsharename[i] ) {
				alert("這個名子有其他人用了喔！請換個名子！");
                        	f.sharename.focus();
                        	flag=false;
                        	return flag;
			}
		}

		// 三個群組或三個用戶，不能都沒選擇喔！
		var myusers   = document.getElementsByName('users[]');
		var mygroups  = document.getElementsByName('groups[]');
		if ( myusers[0].value == "no" &&  myusers[1].value == "no" &&  myusers[2].value == "no" &&  
		     mygroups[0].value == "no" &&  mygroups[1].value == "no" &&  mygroups[2].value == "no" ) {
                        alert("必須有一個以上的用戶或群組選擇才行！");
                        flag=false;
                        return flag;
		}

                return confirm("是否確定上傳上述資料？");
        }

	function nothing() {
		return false ;
	}
</script>

<?php   include( "../include/footer.php" ); ?>
