<?php	
	$buttom		= "mail_server";
	$check_admin 	= "admin";
	$jhtitle     	= "Mail Server";
	include ("../include/header.php");
	include ("./mail_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "yes" ) {
			$myhostname 	= trim(str_replace("\r\n","",$_REQUEST['myhostname']));
			$mydestination 	= $_REQUEST['mydestination'];
			$mydests = "";
			foreach ( $mydestination as $temp ) {
				$temp = trim(str_replace("\r\n","",$temp));
				if ( $temp != "" ) {
					if ( $mydests == "" ) $mydests = $temp; else $mydests = "$mydests, $temp";
				}
			}
			$mynetworks 	= $_REQUEST['mynetworks'];
			$mynets = "";
			foreach ( $mynetworks as $temp ) {
				$temp = trim(str_replace("\r\n","",$temp));
				$temp = trim(str_replace("/","\\/",$temp));
				if ( $temp != "" ) {
					if ( $mynets == "" ) $mynets = $temp; else $mynets = "$mynets, $temp";
				}
			}
			//$mailbox_size_limit 	= trim(str_replace("\r\n","",$_REQUEST['mailbox']))*1000000;
			$mailbox_size_limit 	= 0;
			$message_size_limit 	= trim(str_replace("\r\n","",$_REQUEST['mymesg'])) *1000000;
			$smptd_limit 		= trim(str_replace("\r\n","",$_REQUEST['smptd']));
			$recipient_limit 	= trim(str_replace("\r\n","",$_REQUEST['recipient']));

			//echo "$myhostname|$mydests|$mynets|$mailbox_size_limit|$message_size_limit|$smptd_limit|$recipient_limit";

			$log = exec ( "
				sudo sed -i \"s/config\['default_host'\] =.*$/config\['default_host'\] = 'ssl:\/\/${myhostname}';/g\" /etc/roundcubemail/config.inc.php 2>&1;
				sudo sed -i 's/^myhostname.*$/myhostname = $myhostname/g' 	/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^mydestination.*$/mydestination = $mydests/g' 	/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^mynetworks.*$/mynetworks = $mynets/g' 		/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^mailbox_size_limit.*$/mailbox_size_limit = $mailbox_size_limit/g' 	/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^message_size_limit.*$/message_size_limit = $message_size_limit/g' 	/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^smtpd_recipient_limit.*$/smtpd_recipient_limit = $smptd_limit/g' 	/etc/postfix/main.cf 2>&1;
				sudo sed -i 's/^default_destination_recipient_limit.*$/default_destination_recipient_limit = $recipient_limit/g' 	/etc/postfix/main.cf 2>&1;
			" );

                        // 最終就得要重新啟動 postfix 囉！
                        $log = exec ( " sudo systemctl restart MailScanner  " );
                        echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/mail/mail_show.php';</script>";
			die;
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Mail 寄信伺服器 (MTA) 基本設定組態</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">寄信主機的組態設定與修改</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label class="control-label col-lg-2">設定郵件主機名：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="myhostname" value="<?php echo $mail_hostname; ?>"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">您信件的收件者，會看到發信的來源就是這個主機名稱。要注意，這個主機名稱最好有 MX 標誌以及 IP 設定才好。</span><br/>
                                <span class="form-control-static">ex&gt; mail.your.domain (預設 localhost)</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">收件郵件主機名：</label>
                            <div class="col-lg-4">
                            <?php 
                                foreach ( $mail_dest as $temp ) {
                                    echo "<div class='checkbox'><label><input type='checkbox' name='mydestination[]' value='$temp' checked='checked' />$temp<label></div>";
                                }
                            ?>
                                <input type="text" class="form-control" name="mydestination[]" placeholder='增加其他主機名稱'/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">別人寄信到妳的郵件主機時，使用什麼名稱的情況下，妳的郵件伺服器才會將該信件收下來。基本上，有寫到 myhostname,localhost 的設定，請都不要刪除，那是預設值。其他的就請自行填寫了。</span><br/>
                                <span class="form-control-static">ex&gt; mail.your.domain</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">信任用戶：</label>
                            <div class="col-lg-4">
                            <?php   
                                foreach ( $mail_clients as $temp ) {
                                    echo "<div class='checkbox'><label><input type='checkbox' name='mynetworks[]' value='$temp' checked='checked' />$temp<label></div>";
                                }
                            ?>
                            <input type="text" class="form-control" name="mynetworks[]" placeholder='增加其他網域或IP'/></td>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">可以信任的用戶端，通常只的就是內網了。外部網路盡量不要開放，會有垃圾信攻擊問題。</span><br/>
                                <span class="form-control-static">ex&gt; 192.168.1.0/24</span>
                            </div>
                        </div>
                        <!--- <div class="form-group">
                            <label class="control-label col-lg-2">單封寫入最大容量：</label>
                            <div class="col-lg-4 form-inline">
                                <input type="text" class="form-control" name="mailbox" value="<?php printf("%1.1f",$mail_mbox_size/1000000); ?>"/>M Bytes
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">單封信件來的時候，經過本機的一些垃圾信或者是接收任務等的額外資訊後，最終寫入到本機檔案時，單封信件的最大容量。此容量需要比單封信件接收容量大一些才好。</span><br/>
                                <span class="form-control-static">ex&gt; 100 (預設 50)</span>
                            </div>
                        </div> --->
                        <div class="form-group">
                            <label class="control-label col-lg-2">單信接收最大容量：</label>
                            <div class="col-lg-4 form-inline">
			        <input type="text" class="form-control" name="mymesg" value="<?php printf("%1.1f",$mail_mesg_size/1000000); ?>"/>M Bytes
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">單信來信容量的限制，FB 是 25M， Google 大約 20M，那妳覺得妳需要多大呢？自己設定囉。</span><br/>
                                <span class="form-control-static">ex&gt; 100 (預設 10)</span>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="control-label col-lg-2">單信接收最多用戶：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="smptd" value="<?php echo $mail_smtpd; ?>"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">收下單一來信，有可能會有多個本機收件者。這個設定就是指定最多能夠有幾個本機用戶收下該信件，超過限制就無法收下囉。</span><br/>
                                <span class="form-control-static">ex&gt; 500 (預設 1000)</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-2">單信發送最多用戶：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="recipient" value="<?php echo $mail_recipient; ?>"/>
                            </div>
                            <div class="col-lg-6">
                                <span class="form-control-static">從妳的主機寄信出去時，本郵件伺服器最多可以幫你傳遞的用戶數量有多少，超過此限制則不給你寄信了。</span><br/>
                                <span class="form-control-static">ex&gt; 100 (預設 50)</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-5 col-lg-2">
                                <input type="hidden" name="action" value="yes" />
                                <button type="submit" class="btn btn-primary btn-block">開始修改</button>
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
	function checkme(f) {
		return confirm("是否確定修改設定呢？\n\n因為重新啟動郵件主機會花較多時間，請耐心等候數秒鐘..");
	}
</script>

<?php   include("../include/footer.php"); ?>
