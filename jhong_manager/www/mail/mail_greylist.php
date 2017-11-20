<?php	
	$buttom		= "mail_server";
	$check_admin 	= "admin";
	$jhtitle     	= "Mail Server Greylist";
	include ("../include/header.php");
	include ("./mail_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "greylist" ) {
			$getgreylist = $_REQUEST['getgreylist'];
			$mesg = "";
			foreach ( $getgreylist as $list ) {
				if ( trim(str_replace("\r\n","",$list)) != "" ) {
					if ( $mesg == "" ) {
						$mesg = $list;
					} else {
						$mesg = "${mesg}\n$list";
					}
				}
			}
			if ( $mesg == "" ) {
				$log = exec ( "sudo rm /etc/postfix/postgrey_whitelist_clients.local; sudo touch /etc/postfix/postgrey_whitelist_clients.local " );
			} else {
				$log = exec ( "sudo bash -c \"echo '$mesg' >  /etc/postfix/postgrey_whitelist_clients.local \" " );
			}

			$log = exec ( "sudo systemctl restart postgrey " );
			echo "<script>location.href='$web/mail/mail_greylist.php';</script>";
		}

		if ( $_REQUEST['action'] == "close" ) {
			$log = exec ( "sudo bash -c \" line=\\\$(grep -n 'check_policy_service unix:/var' /etc/postfix/main.cf | cut -d ':' -f1); if [ \\\"\\\$line\\\" != '' ]; then sed -i \\\"\\\${line}d\\\" /etc/postfix/main.cf ; fi \" " );
			echo "<script>location.href='$web/mail/mail_greylist.php';</script>";
		}

		if ( $_REQUEST['action'] == "active" ) {
			$log = shell_exec ( "sudo bash -c \"line=\\\$(grep -n 'reject_unauth_destination' /etc/postfix/main.cf | cut -d ':' -f1); if [ \\\"\\\$line\\\" != '' ]; then sed -i \\\"\\\${line}a \ \ check_policy_service unix:/var/spool/postfix/postgrey/socket,\\\" /etc/postfix/main.cf ; fi  \" 2>&1" );
			echo "<script>location.href='$web/mail/mail_greylist.php';</script>";
		}

		if ( $_REQUEST['action'] == "spamlist" ) {
			$spamlist = $_REQUEST['spamlist'];
			$mesg = "";
			foreach ( $spamlist as $list ) {
				if ( trim(str_replace("\r\n","",$list)) != "" ) {
					if ( $mesg == "" ) {
						$mesg = $list;
					} else {
						$mesg = "${mesg}\n$list";
					}
				}
			}
			if ( $mesg == "" ) {
				$mesg = "FromOrTo:	default 	no";
			} else {
				$mesg = "${mesg}\nFromOrTo:	default 	no";
			}

			$log = exec ( "sudo bash -c \"echo '$mesg' >  /etc/MailScanner/rules/spam.whitelist.rules \" " );
		}
                       /* // 最終就得要重新啟動 postfix 囉！
                        $log = exec ( " sudo systemctl restart MailScanner  " );
                        echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/mail/mail_show.php';</script>";
			die;*/
	}

	$checkfile = exec ( "[ -f /etc/MailScanner/rules/spam.whitelist.rules ] && echo 'OK' || echo 'false' " );

	if ( $checkfile == "OK" ) {
		$spam_file = fopen ( "/etc/MailScanner/rules/spam.whitelist.rules", "r" ) or die ("Can't open \"/etc/MailScanner/rules/spam.whitelist.rules\" file");
		while ( ! feof($spam_file) ) {
			$temp_info = fgets($spam_file);
			$temp_info = trim(str_replace("\r\n","",$temp_info));
			if ( ! preg_match("/^#/",$temp_info ) && ! preg_match("/FromOrTo:[[:space:]]default/",$temp_info) && $temp_info != "" ) {
				$spam_list[] = $temp_info;
			}
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Mail 寄信伺服器 (MTA) 灰、白名單設定</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">設定廣告信來源白名單 (greylist)</div>
                    <div class="panel-body">
                        <form method="post" action="mail_greylist.php" OnSubmit="return checkgreylist(this)">
                            <p>郵件伺服器的垃圾信抵擋機制 Postgrey 目前的運作狀態如下：</p>
                            <?php
                                if ( $proc_greylist == "OK" ) {
                                    echo "<span class='text-primary'>目前支援中</span>";
                                    echo "<a href='mail_greylist.php?action=close' class='btn btn-primary' Onclick='return changepostgrey()'>點我關閉</a></span>";
                                } else {
                                    echo "<span class='text-danger'>目前關閉中</span>";
                                    echo "<a href='mail_greylist.php?action=active' class='btn btn-primary' Onclick='return changepostgrey()'>點我啟用支援</a></span>";
                                }
                            ?> 
                            <hr/>
                            <p>目前已有的白名單內容如下：</p>
                            <?php
                                foreach ( $mail_greylist as $list ) {
                                    echo "<div class='checkbox'><label><input type='checkbox' checked='checked' name='getgreylist[]' value='$list' />$list</label></div>";
                                }
                            ?>
                            <hr/>
                            <p>底下請填寫 IP 或主機名稱，一次填寫一個，按下開始修改後，可以再加或勾選上面既有的來刪減白名單。</p>
                            <div class="col-lg-4">
                                <input type='text' class="form-control" name='getgreylist[]' placeholder="ex> gmail.com" />
                            </div>
                            <input type="hidden" name="action" value="greylist" />
                            <div class="clearfix"></div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">開始修改</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">設定廣告信白名單 (spamassassin)</div>
                    <div class="panel-body">
                        <form method="post" action="mail_greylist.php" OnSubmit="return checkgreylist(this)">
                            <p>目前已有的白名單規則內容如下：</p>
<pre style="border: 0; background-color: transparent; padding: 0; margin: 0;">
<?php
    foreach ( $spam_list as $list ) {
        echo "<div class='checkbox'><label><input type='checkbox' checked='checked' name='spamlist[]' value='$list' />$list</label></div>";
    }
?></pre>
                            <hr/>
                            <p>底下請填寫白名單規則喔！一次填寫一個，按下開始修改後，可以再加或勾選上面既有的來刪減白名單。</p>
                            <div class="col-lg-4">
                                <input type='text' name='spamlist[]' class="form-control" placeholder="ex> From:   your.domain.   yes" />
                            </div>
                            <input type="hidden" name="action" value="spamlist" />
                            <div class="clearfix"></div>
                            <pre style="border: 0; background-color: transparent;">範例：
        針對 I P網段 -&gt; From:   152.78.          yes
        針對主機名稱 -&gt; From:   host:cracker.cn  yes</pre>
                            <div class="clearfix"></div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">開始修改</button>
                            </div>
                        </form>
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
	function checkgreylist(f) {
		return confirm("是否確定修改白名單資料庫..");
	}
	function changepostgrey() {
		return confirm("是否確定修改支援情況..");
	}
</script>

<?php   include("../include/footer.php"); ?>
