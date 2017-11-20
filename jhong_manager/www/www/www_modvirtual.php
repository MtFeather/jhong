<?php	
	$buttom		= "apache";
	$check_admin 	= "admin";
	$jhtitle     	= "WWW 服務";
	include("../include/header.php");
	include("./www_function.php");
	include("../account/function.php");

	if ( ! isset ( $_REQUEST['action'] ) ) {
		echo "無法使用本網頁...";
		include ( "../include/footer.php" );
		die;
	}

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "manage" ) {
			$docroot = $_REQUEST['docroot'];
			$thisline = 0;
			if ( $wwwvnumber > 0 ) {
				for ( $i=1; $i<=$wwwvnumber; $i++ ) {
					$docroot_old = explode ( "/", $www_v_documentroot[$i] );
					if ( $docroot == $docroot_old[3] ) {
						$thisline = $i;
						 break;
					}
				}
			}
		}
	}

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {
			$servername       = str_replace( " ", "", $_REQUEST['servername']);
			$serveralias      = str_replace( " ", "", $_REQUEST['serveralias']);
			$serveradmin      = str_replace( " ", "", $_REQUEST['serveradmin']);
			$documentroot     = str_replace( " ", "", $_REQUEST['documentroot']);
			$documentrootabs  = "/jhong/www/${documentroot}";
			$allowoverride    = $_REQUEST['allowoverride'];
			$options          = $_REQUEST['options'];
			$jhongadmin       = $_REQUEST['jhongadmin'];

			if ( $serveralias == "" ) $serveralias = $servername;

			if ( count($options) == 0 ) {
				$optionsall = "None";
			} else {
				$optionsall = "";
				foreach ( $options as $temp ) {
					if ( $optionsall == "" ) {
						$optionsall = trim($temp) ;
					} else {
						$optionsall = $optionsall . " " . trim($temp) ;
					}
				}
			}

			$docroot = $documentroot;
			$thisline = 0;
			if ( $wwwvnumber > 0 ) {
				for ( $i=1; $i<=$wwwvnumber; $i++ ) {
					$docroot_old = explode ( "/", $www_v_documentroot[$i] );
					if ( $docroot == $docroot_old[3] ) {
						$thisline = $i;
						 break;
					}
				}
			}

			echo "$servername|$serveralias|$serveradmin|$documentroot|$documentrootabs|$allowoverride|$optionsall|$jhongadmin <br /><br />";
			echo "$www_v_jhongadmin[$thisline]";

			// 若管理員不同，才進行卸載與重新掛載
                        if ( $www_v_jhongadmin[$thisline] != $jhongadmin ) {
                                $log = shell_exec ( "sudo sh /jhong/bin/wwwmount.sh umount $documentrootabs $documentroot $www_v_jhongadmin[$thisline]" );
                                $log = shell_exec ( "sudo sh /jhong/bin/wwwmount.sh  mount $documentrootabs $documentroot $jhongadmin" );
                        }

			// 設定這個主網頁的虛擬主機
			$log = shell_exec ( "sudo bash -c \"echo '
<VirtualHost *:80>
    ServerName $servername
    ServerAlias $serveralias
    DocumentRoot $documentrootabs
    ServerAdmin $serveradmin
</VirtualHost>

<Directory \\\"$documentrootabs\\\">
    Options $optionsall
    AllowOverride $allowoverride
    Order allow,deny
    Allow from all
</Directory>
#jhongadmin $jhongadmin' > /etc/httpd/conf.d/jhong_${documentroot}.conf \" " );

			// 最終就得要重新啟動 apache 囉！
			$log = exec ( "sudo systemctl restart httpd" );
			echo "<script>alert('新建完畢，請檢查輸出結果'); location.href='$web/www/www_virtuals.php';</script>";
			die;
		}
	}

	if ( ! isset ( $thisline ) || $thisline == 0 ) {
		echo "找不到正確的虛擬機設定檔，無法繼續！";
		include ( "../include/footer.php" );
		die;
	}

?>
	<h1>修改 WWW 虛擬主機資訊</h1>

	<form method="post" action="<?php echo $web; ?>/www/www_modvirtual.php" OnSubmit="return checkgo(this)">
 	<table class="account_table">
                <tr>
                        <th style="font-size:10pt; width: 100px;">項目名稱</th>
                        <th style="font-size:10pt;">項目內容</th>
                        <th style="font-size:10pt;">說明</th>
                </tr>
		<tr>
			<td style="line-height: 1.1;">伺服器名稱<br />(ServerName)</td>
			<td><input type="text" name="servername" value="<?php echo $www_v_servername[$thisline] ?>"/></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">請填寫一個有 IP 的主機名稱！
			<br />主機名稱開頭只能是英文，且全部字元只能是英文、數字、底線與句號 (.)</td>
		</tr>
		<tr>
			<td style="line-height: 1.1;">伺服器別名<br />(ServerAlias)</td>
			<td><input type="text" name="serveralias" value="<?php echo $www_v_serveralias[$thisline] ?>"/></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">若有多個主機別名，可以使用空格來隔開，也能使用 * 萬用字元，
			類似 *.example.com 的用法喔！
			</td>
		</tr>
		<tr>
			<td style="line-height: 1.1;">管理員 email<br />(ServerAdmin)</td>
			<td><input type="text" name="serveradmin" value="<?php echo $www_v_serveradmin[$thisline] ?>"/></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">這部伺服器出問題應該要找誰呢？
			<br />請填寫一個合法的 email 喔！</td>
		</tr>
		<tr>
			<td style="line-height: 1.1;">系統根目錄<br />(DocumentRoot)</td>
			<td style="text-align:left;">/<?php echo $docroot; ?><input type="hidden" name="documentroot" value="<?php echo $docroot; ?>"/></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">WWW 根目錄的所在目錄
			<br />只能是英文與數字及底線的組成，不可以有 / 的存在。
			<br />一經建立後，只能刪除而不能修改喔！所以設定前請確認清楚！</td>
		</tr>
		<tr>
			<td>伺服器特性參數</td>
			<td style='text-align:left;'>
			<?php
				$temp[1]="Indexes"; $temp[2]="Includes"; $temp[3]="FollowSymLinks"; 
				$temp[4]="SymLinksifOwnerMatch"; $temp[5]="ExecCGI"; $temp[6]="MultiViews";
				for ( $i=1; $i<=6; $i++) {
					$checked="";
					foreach ( $www_v_options[$thisline] as $tempgo ) {
						if ( $temp[$i] == $tempgo ) $checked="checked='checked'";
					}
					echo "<input type='checkbox' name='options[]' value='$temp[$i]' $checked />$temp[$i]<br />";
				}
			?></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">通常至少要有 FollowSymLinks, 如果想要支援沒 index.html
			就顯示所有檔案的話，那可以支援 Indexes。其他的就按照您自己的環境勾選吧！</td>
		</tr>
		<tr>
			<td>允許 .htaccess</td>
			<td style="text-align:left;"><select name="allowoverride" >
				<option value="None" />無支援</option>
				<option value="AuthConfig" <?php if ( $www_v_allowoverride[$thisline] == "AuthConfig" ) echo "selected='selected'"; ?>/>開始支援</option>
			</select></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">看看是否支援使用者自行設定 .htaccess 的功能</td>
		<tr>
			<td>WWW 管理員</td>
			<td style='text-align:left;'><select name="jhongadmin">
			<?php
				for ( $i=1; $i<=$usernumber; $i++ ) {
					if ( $my_user_bk[$i] == "no" ) {
						$checked="";
						if ( $my_username[$i] == $www_v_jhongadmin[$thisline] ) $checked="selected='selected'";
						echo "<option value='$my_username[$i]' $checked />$my_username[$i]</option>";
					}
				}
			?></select></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">本系統會在此用戶家目錄下建置一個連結檔，
			檔名預設為管理者家目錄下的： <span style="color:red; font-weight: bolder;">/www_<?php echo $www_servername; ?></span>。
			建議使用 FTP 服務上傳您的網頁喔！
			</td>
		</tr>
		</tr>
		<tr>
			<td>確認修改</td>
			<td style='text-align:left;'>
			<input type="hidden" name="action" value="yes" />
			<input type="submit" value="開始修改" class="table_button" /></td>
			<td style="font-size:8pt; line-height: 1.1; text-align:left;">上述的資料有修改過才需要按下此按鈕，否則請勿按下此按鈕喔！
			不然系統會重新啟動 WWW 伺服器的。</td></tr>
        </table>
	</form>

<script>
	function checkgo(f) {
                var flag=true;
                var re  = /^[A-Za-z][A-Za-z0-9_.-]+$/;
                var re2 = /^[A-Za-z0-9_-]+$/;
                if (!re.test(f.servername.value)) {
                        alert("你的主機名稱開頭必須要英文，且只能是英文、數字及底線 ( _ 或 - ) 與逗號 (.) 的組合，不可輸入其他字元喔！");
                        f.servername.focus();
                        flag=false;
                        return flag;
                }
                if (!re2.test(f.documentroot.value)) {
                        alert("你的首頁目錄只能是英文、數字及底線 ( _ 或 - ) 的組合，不可輸入其他字元喔！");
                        f.documentroot.focus();
                        flag=false;
                        return flag;
                }

		return confirm("是否確認要開始修改 WWW 的虛擬主機設定值？");
	}
</script>

<?php   include("../include/footer.php"); ?>
