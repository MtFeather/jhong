<?php 
	$buttom      = "samba_server";
	$check_admin = "admin";
	$jhtitle     = "samba 資訊";
	include ( "../include/header.php" );
	include ( "./samba_function.php" );

	// 如果有其他的動作要進行時
	if ( isset ( $_REQUEST['action'] ) ) {

		// 先進行 [global] 修改的動作喔！
		if ( $_REQUEST['action'] == "yes" ) {
			$workgroup    = trim($_REQUEST['workgroup']);
			$netbiosname  = trim($_REQUEST['netbiosname']);
			$serverstring = trim($_REQUEST['serverstring']);

			$log = shell_exec ( "sudo bash -c \" sed -i 's/^[[:space:]]*workgroup.*$/\\tworkgroup = $workgroup/g' /etc/samba/smb.conf; sed -i 's/^[[:space:]]*netbios name.*$/\\tnetbios name = $netbiosname/g' /etc/samba/smb.conf; sed -i 's/^[[:space:]]*server string.*$/\\tserver string = $serverstring/g' /etc/samba/smb.conf \" 2>&1 " );

			$log = exec ( "sudo systemctl restart smb nmb" );
			echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/samba/samba_show.php';</script>";
		}

		// 再進行刪除的動作
		if ( $_REQUEST['action'] == "delete" ) {
			$sharename = $_REQUEST['sharename'];
			if ( $smbnumber > 0 ) {
				for ($i=1; $i<=$smbnumber; $i++ ) {
					if ( $smb_sharename[$i] == $sharename ) {
						$thisdirname = $smb_dirname[$i];
						$thisfilename = $smb_filename[$i];
						$thisline = exec ( "grep -n 'include = $thisfilename' /etc/samba/smb.conf | cut -d ':' -f1" );
					}
				}
			}
			$log = exec ( "sudo bash -c \" rm -r $thisdirname; rm $thisfilename; sed -i '${thisline}d' /etc/samba/smb.conf \" " );
			$log = exec ( "sudo systemctl restart smb nmb" );
			echo "<script>alert('刪除完畢，請檢查列表資訊'); location.href='$web/samba/samba_show.php';</script>";
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux  - 網芳資訊</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">整體伺服器設定 [global]</div>
                        <div class="panel-body">
                            <form class="form-horizontal" method="post" name="adduserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                                <div class='form-group'>
                                    <label class="control-label col-lg-2">工作群組：</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" name="workgroup" value="<?php echo $smb_server_workgroup; ?>"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="form-control-static">可以修改成妳的 Windows 網路芳鄰工作群組相同。</span><br/>
                                        <span class="form-control-static text-danger">輸入 3~16 個英文、數字、底線的組合才好！</span>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class="control-label col-lg-2">主機名稱：</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" name="netbiosname" value="<?php echo $smb_server_netbiosname ; ?>"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="form-control-static">顯示這個主機名稱而以，不一定要與網路名稱相同。</span><br/>
                                        <span class="form-control-static text-danger">最好與工作群組不同，但設定的限制相同！</span>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class="control-label col-lg-2">伺服器說明：</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" name="serverstring" value="<?php echo $smb_server_serverstring ; ?>"/>
                                    </div>
                                    <div class="col-lg-6">
                                        <span class="form-control-static">單純的說明這部伺服器而已。</span><br/>
                                        <span class="form-control-static text-danger">還是需要使用英文來進行說明喔！</span>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class="control-label col-lg-2">Windows 編碼：</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" value="<?php echo $smb_server_doscharset; ?>" disabled/>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="form-control-static">Windows 的預設編碼為繁體中文</p>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <label class="control-label col-lg-2">Server 編碼：</label>
                                    <div class="col-lg-4">
                                        <input type="text" class="form-control" value="<?php echo $smb_server_doscharset; ?>" disabled/>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="form-control-static">伺服器 Server 端的預設編碼為萬國碼</p>
                                    </div>
                                </div>
                                <div class='form-group'>
                                    <div class="col-lg-offset-4 col-lg-4">
                                        <input type="hidden" name="action" value="yes" />
                                        <button type="submit" class='btn btn-primary btn-block'>若要修改才按下這個按鈕，否則無須更動！</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">個別分享的資源列表</div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>分享名稱與目錄</th>
                                        <th>容量(MB)</th>
                                        <th>是否可寫入</th>
                                        <th>讀寫群組</th>
                                        <th>唯讀群組</th>
                                        <th>讀寫用戶</th>
                                        <th>修改/刪除</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php   // 開始顯示各項資料
                                        if ( $smbnumber > 0 ) {
                                            for ( $i=1; $i<=$smbnumber; $i++ ) {
                                                echo "<tr><td>$smb_sharename[$i]<br />\\\\Server_IP\\$smb_sharename[$i]</td><td>$smb_du[$i]</td><td>$smb_writable[$i]</td><td>";
                                                foreach ( $smb_perm_group[$i] as $temp ) echo "${temp}<br />";
                                                echo "</td><td>";
                                                foreach ( $smb_perm_groupro[$i] as $temp ) echo "${temp}<br />";
                                                echo "</td><td>";
                                                foreach ( $smb_perm_user[$i] as $temp ) echo "${temp}<br />";
                                                echo "</td><td><a href='?sharename=$smb_sharename[$i]&action=delete'>";
                                                echo "<button type='submit' class='btn btn-danger' OnClick='return mydel()'>刪除</button></a> ";
                                                echo "<a href='samba_mod.php?sharename=$smb_sharename[$i]'>";
                                                echo "<button type='submit' class='btn btn-primary'>管理</button></a></td></tr>";
                                            }
                                        }
                                    ?> 
                                </tbody>
                            </table>
                        </div>
                        <!-- /.table-responsive -->
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
            <!-- /.panel-group  -->
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
                var re = /^[A-Za-z][A-Za-z0-9_-]+$/;
                if (!re.test(f.workgroup.value)) {
                        alert("工作群組開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.workgroup.focus();
                        flag=false;
                        return flag;
                }
                if ( f.workgroup.value.length < 3 || f.workgroup.value.length > 16) {
                        alert("工作群組名稱必須要有 3 ~ 16 個字元之間！");
                        f.workgroup.focus();
                        flag=false;
                        return flag;
                }

		// 這個是 netbiosname 的檢查 
                if (!re.test(f.netbiosname.value)) {
                        alert("主機名稱開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.netbiosname.focus();
                        flag=false;
                        return flag;
                }
                if ( f.netbiosname.value.length < 3 || f.netbiosname.value.length > 16) {
                        alert("主機名稱必須要有 3 ~ 16 個字元之間！");
                        f.netbiosname.focus();
                        flag=false;
                        return flag;
                }

		// 這個是 serversting 的檢查 
                if (!re.test(f.serverstring.value)) {
                        alert("說明資料開頭必須要英文，且只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                        f.serverstring.focus();
                        flag=false;
                        return flag;
                }

                return confirm("是否確定上傳上述資料？");
        }

	function nothing() {
		return false ;
	}

	function mydel() {
		var msgmydo = confirm("是否確定刪除此資源呢？\n\n按下確定後，分享目錄內任何檔案均會被刪除喔！");
		return msgmydo;
	}
</script>
<?php   include( "../include/footer.php" ); ?>
