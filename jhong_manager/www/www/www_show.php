<?php	
	$buttom		= "apache";
	$check_admin 	= "admin";
	$jhtitle     	= "WWW 服務";
	include("../include/header.php");
	include("./www_function.php");
	include("../account/function.php");

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$listen        = str_replace( " ", "", $_REQUEST['listen']);
			$servername    = str_replace( " ", "", $_REQUEST['servername']);
			$serveradmin   = str_replace( " ", "", $_REQUEST['serveradmin']);
			$documentroot  = str_replace( " ", "", $_REQUEST['documentroot']);
			$allowoverride = $_REQUEST['allowoverride'];
			$options       = $_REQUEST['options'];
			$jhongadmin    = $_REQUEST['jhongadmin'];

			if ( count($options) == 0 ) {
				$optionsall = "None";
			} else {
				$optionsall = "";
				foreach ( $options as $temp ) {
					if ( $optionsall == "" ) {
						$optionsall = trim($temp) ;
					} else {
						$optionsall = $optionsall . "_" . trim($temp) ;
					}
				}
			}

			// 得要判斷目錄是否存在，若不存在就得要丟棄這次的設定
			if ( ! is_dir($documentroot) ) {
				echo "<script>alert('因為 $documentroot 並不存在，所以無法設定'); location.href='$web/www/www_show.php';</script>";
				die;
			}

			//echo "$listen|$servername|$serveradmin|$documentroot|$optionsall|$allowoverride <br />";
			//echo "sudo sh $sdir/www/www_config.sh $listen $servername $serveradmin $documentroot $optionsall $allowoverride $www_documentroot" ;
			$log = shell_exec ( "sudo sh $sdir/www/www_config.sh $listen $servername $serveradmin $documentroot $optionsall $allowoverride $www_documentroot" );
			$msg = shell_exec ( "sudo /sbin/apachectl configtest 2>&1 " );

			// 開始掛載管理員操作的狀態
			if ( $www_jhongadmin != $jhongadmin ) {
				$log = shell_exec ( "sudo sh /jhong/bin/wwwmount.sh umount $www_documentroot $www_servername $www_jhongadmin" );
				$log = shell_exec ( "sudo sh /jhong/bin/wwwmount.sh  mount $documentroot     $servername     $jhongadmin" );
				$log = shell_exec ( "sudo sed -i 's/^#jhongadmin.*$/#jhongadmin $jhongadmin/g' /etc/httpd/conf/httpd.conf " );
			}

			// 設定這個主網頁的虛擬主機
			$optionsall = str_replace ( "_", " ", $optionsall );
			$log = shell_exec ( "sudo bash -c \"echo '
<VirtualHost *:80>
    ServerName    $servername
    DocumentRoot  $documentroot
    ServerAdmin   $serveradmin
</VirtualHost>

<Directory \\\"$documentroot\\\">
    Options $optionsall
    AllowOverride $allowoverride
    Order allow,deny
    Allow from all
</Directory>
#jhongadmin $jhongadmin' > /etc/httpd/conf.d/jhong_00localhost.conf \" " );

			// 最終就得要重新啟動 apache 囉！
			$log = exec ( "sudo systemctl restart httpd" );
			echo "<script>alert('修改完畢，請檢查輸出結果'); location.href='$web/www/www_show.php';</script>";
			die;
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">WWW 服務資訊</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form method="post" action="<?php echo $web; ?>/www/www_show.php" OnSubmit="return checkgo()">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="col-lg-2 text-center">項目名稱</th>
                                <th class="col-lg-3 text-center">項目內容</th>
                                <th class="col-lg-7 text-center">說明</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">監聽埠口</td>
                                <td style="vertical-align: middle;"><input type="text" class="form-control" name="listen" value="<?php echo $www_listen; ?>" /></td>
                                <td style="vertical-align: middle;">監聽的埠口，全世界預設就是 80 ，不過也有 8080 埠口的設定喔！</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">伺服器名稱</td>
                                <td style="vertical-align: middle;"><input type="text" class="form-control" name="servername" value="<?php echo $www_servername; ?>" /></td>
                                <td style="vertical-align: middle;">請填寫一個主機名稱！</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">管理員 email</td>
                                <td style="vertical-align: middle;"><input type="text" class="form-control" name="serveradmin" value="<?php echo $www_serveradmin; ?>" /></td>
                                <td style="vertical-align: middle;">這部伺服器出問題應該要找誰呢？</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">系統根目錄</td>
                                <td style="vertical-align: middle;"><input type="text" class="form-control" name="documentroot" value="<?php echo $www_documentroot; ?>" /></td>
                                <td style="vertical-align: middle;">WWW 根目錄的所在目錄</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">這個網頁的參數</td>
                                <td>
                                <?php
                                    $temp[1]="Indexes"; $temp[2]="Includes"; $temp[3]="FollowSymLinks";
                                    $temp[4]="SymLinksifOwnerMatch"; $temp[5]="ExecCGI"; $temp[6]="MultiViews";
                                    for ( $i=1; $i<=6; $i++) {
                                        $checked="";
                                        foreach ( $www_options as $temp2 ) {
                                            if ( $temp[$i] == $temp2 ) $checked="checked='checked'";
                                        }
                                        echo "<div class='checkbox'><label><input type='checkbox' name='options[]' value='$temp[$i]' $checked />$temp[$i]</label></div>";
                                    }
                                ?>
                                </td>
                                <td style="vertical-align: middle;">
                                    通常至少要有 FollowSymLinks, 如果想要支援沒 index.html就顯示所有檔案的話，那可以支援 Indexes。其他的就按照您自己的環境勾選吧！
                                </td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">允許 .htaccess</td>
                                <td style="vertical-align: middle;">
                                   <select name="allowoverride" class="form-control">
                                       <option value="None" <?php if ( $www_allowoverride == "None" ) echo "selected='selected'"; ?> />無支援</option>
                                       <option value="AuthConfig" 
                                       <?php if ( $www_allowoverride == "AuthConfig" ) echo "selected='selected'"; ?> />開始支援</option>
                                    </select>
                                </td>
                                <td style="vertical-align: middle;">看看是否支援使用者自行設定 .htaccess 的功能</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">WWW 管理員</td>
                                <td style="vertical-align: middle;">
                                    <select name="jhongadmin" class="form-control">
                                    <?php
                                        for ( $i=1; $i<=$usernumber; $i++ ) {
                                            if ( $my_user_bk[$i] == "no" ) {
                                                $checked="";
                                                if ( $my_username[$i] == $www_jhongadmin ) $checked="selected='selected'";
                                                echo "<option value='$my_username[$i]' $checked />$my_username[$i]</option>";
                                            }
                                        }
                                    ?>
                                    </select>
                                </td>
                                <td style="vertical-align: middle;">本系統會在此用戶家目錄下建置一個連結檔，檔名預設為管理者家目錄下的： <span class="text-danger">/www_<?php echo $www_servername; ?></span>。建議使用 FTP 服務上傳您的網頁喔！</td>
                            </tr>
                            <tr>
                                <td style="vertical-align: middle; text-align: center;">確認修改</td>
                                <td style="vertical-align: middle;">
                                    <input type="hidden" name="action" value="yes" />
                                    <button type="submit" class="btn btn-primary">開始修改</button>
                                </td>
                                <td style="vertical-align: middle;">上述的資料有修改過才需要按下此按鈕，否則請勿按下此按鈕喔！不然系統會重新啟動 WWW 伺服器的。</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- /.table-responsive -->
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<script>
	function checkgo() {
		return confirm("是否確認要開始修改 WWW 的設定值？");
	}
</script>

<?php   include("../include/footer.php"); ?>
