<?php 
	$buttom      = "dns_server";
	$check_admin = "admin";
	$jhtitle     = "增加 DNS 的網域正解設定";
	include ( "../include/header.php" );
	include ( "./dns_function.php" );

	if ( isset ( $_REQUEST['zonename'] ) ) {
		$zonename = $_REQUEST['zonename'];
	} else {
		$zonename = "";
	}

	if ( ! isset ( $_REQUEST['ttl'] ) ) {
		$ttl = "600";
	} else {
		$ttl = $_REQUEST['ttl'];
	}

	if ( ! isset ( $_REQUEST['serial'] ) ) {
		$serial = date("Ymd") . "01";
	} else {
		$serial = $_REQUEST['serial'];
	}

	if ( ! isset ( $_REQUEST['dnsservername'] ) ) {
		$dnsservername = "dns";
	} else {
		$dnsservername = $_REQUEST['dnsservername'];
	}

	if ( ! isset ( $_REQUEST['dnsserverip'] ) ) {
		$dnsserverip = shell_exec ( "ifconfig | grep 'inet ' | grep -v '127.0.0.1'| tail -n 1 | awk '{print \$2}'" );
	} else {
		$dnsserverip = $_REQUEST['dnsserverip'];
	}

	if ( ! isset ( $_REQUEST['mailservername'] ) ) {
		$mailservername = $zonename;
	} else {
		$mailservername = $_REQUEST['mailservername'];
	}

	if ( ! isset ( $_REQUEST['mailserverip'] ) ) {
		$mailserverip = shell_exec ( "ifconfig | grep 'inet ' | grep -v '127.0.0.1'| tail -n 1 | awk '{print \$2}'" );
	} else {
		$mailserverip = $_REQUEST['mailserverip'];
	}

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {

			//echo "$zonename|$ttl|$serial|$dnsservername|$dnsserverip|$mailservername|$mailserverip <br />";
			$sethostname = $_REQUEST['sethostname'];
			$setip       = $_REQUEST['setip'];

			$mymesg = "";
			for ( $i=0; $i<=2; $i++ ) {
				//echo "$sethostname[$i]--$setip[$i]|";
				if ( $sethostname[$i] != "" ) $mymesg = $mymesg . "$sethostname[$i]	IN A	$setip[$i]\n";
			}

			$log = shell_exec ( "sudo bash -c \" echo 'zone \\\"$zonename\\\" { type master; file \\\"named.$zonename\\\"; };' >> /etc/named/named.jhong.conf \"" );

			$log = shell_exec ( "sudo bash -c \" echo '\\\$TTL	$ttl
@	IN SOA	${zonename}. root@${zonename}. ( $serial 1D 1H 1W 3H )
@	IN NS	$dnsservername
$dnsservername	IN A	$dnsserverip
@	IN MX 10	${mailservername}.
${mailservername}.	IN A	$mailserverip

$mymesg' > /var/named/named.$zonename \" " );

			$log = exec ( "sudo dos2unix /var/named/named.$zonename" );

			// 最終就得要重新啟動 dns 囉！
			$log = exec ( "sudo systemctl restart named-chroot" );
			echo "<script>alert('建置完畢，請檢查列表資訊'); location.href='$web/dns/zone_show.php';</script>";
			die;
		}
	}
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">增加 DNS 的正解領域設定</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" name="addsambaform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return check(this)">
                <div class="panel panel-default">
                    <div class="panel-heading">新增 DNS 的正解設定</div>
                    <div class="panel-body">
                    <?php   if ( $zonename == "" ) { ?>
                        <div class="form-group">
                            <label class="control-label col-lg-3">正解的網域名稱：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" name="zonename"/>
                            </div>
                            <div class="col-lg-5">
                                <span class="form-control-static text-danger">ex&gt; jhong.com.tw</span><br/>
                                <span class="form-control-static text-danger">這個名稱必須是唯一，且不能與網路其他領域名稱相同；</span><br/>
                                <span class="form-control-static text-danger">同時這個名稱只能是英文、數字與 - _ 的組合才行！</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-5 col-lg-2">
                                <button type="submit" class="btn btn-primary btn-block">按此開始設定</button>
                            </div>
                        </div>
                        <script>
                           function check(f) {
                               // 先抓出目前的 zone 名稱，因為不可以重複喔！
                               var existzonename = new Array();
                               <?php   for ( $i=1; $i<=$dnsnumber; $i++ ) { echo "existzonename[$i] = '$dns_zonename[$i]' ;\n"; } ?>
                                   var totalname = <?php echo $dnsnumber; ?> ;
  
                                   // 先看 zonename 的設定是否符合正確！
                                   var flag=true;
                                   var re = /^[A-Za-z][A-Za-z0-9._-]+$/;
                                   if (!re.test(f.zonename.value)) {
                                       alert("領域名稱只能是英文、數字及底線( _ 或 - )的組合，不可輸入其他字元喔！");
                                       f.zonename.focus();
                                       flag=false;
                                       return flag;
                                   }
                                  if ( f.zonename.value.length < 5 ) {
                                       alert("領域名稱名稱必須要有 5 個字元以上！");
                                       f.zonename.focus();
                                       flag=false;
                                       return flag;
                                  }
                                  for ( i=1; i<=totalname; i++ ){
                                       if ( f.zonename.value == existzonename[i] ) {
                                           alert("這個領域名稱已經存在了喔！請換個領域！");
                                           f.zonename.focus();
                                           flag=false;
                                           return flag;
                                       }
                                  }
                              }
                        </script>
                    <?php   } else { ?>
                        <div class="form-group">
                            <label class="control-label col-lg-3">正解的網域名稱：</label>
                            <div class="col-lg-4">
                                <input type="text" class="form-control" value="<?php echo $zonename; ?>" disabled/>
                                <input type="hidden" name="zonename" value="<?php echo $zonename; ?>" />
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">底下為處理此領域名設定的相關資訊。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">TTL 數值：</label>
                            <div class="col-lg-4">
                                <select name="ttl" class="form-control">
                                <?php $tempa[1] = "300"; $tempa[2]="600"; $tempa[3]="3600"; $tempa[4]="43200"; $tempa[5]="86400";
                                    for ( $i=1; $i<=5; $i++ ) {
                                        echo "<option value='$tempa[$i]' ";
                                        if ( $ttl == $tempa[$i] ) echo "selected='selected' ";
                                        echo "/>$tempa[$i]</option>";
                                    }
                                ?>
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <span class="form-control-static">你的紀錄資訊可以存在其他 DNS 伺服器暫存器多久之意。</span><br>
                                <span class="form-control-static">變動中的伺服器可以調小一些，穩定的伺服器名稱可以久一些。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">此設定之序號：</label>
                            <div class="col-lg-4">
                                <input type="text" name="serial" class="form-control" readonly='readonly' value='<?php echo $serial; ?>' />
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">判斷此設定新舊的序號，自動新增，無須更動</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">DNS 主機名：</label>
                            <div class="col-lg-4 form-inline">
                                <input type="text" name="dnsservername" class="form-control" value='<?php echo $dnsservername; ?>'/>.<?php echo $zonename; ?>
                            </div>
                            <div class="col-lg-5">
                                <span class="form-control-static">其實就是這部伺服器的主機名稱，用來管理 DNS 設定者，也是 NS 標誌。一般來說，使用本系統預設的設定值即可。同時與底下的 IP 要互相對應才行。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">DNS 主機 IP：</label>
                            <div class="col-lg-4">
                                <input type="text" name="dnsserverip" class="form-control" value='<?php echo $dnsserverip; ?>'/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">管理妳 DNS 領域的伺服器 IP ，就這部伺服器。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">郵件交換主機名：</label>
                            <div class="col-lg-4">
                                <input type="text" name="mailservername" class="form-control" value='<?php echo $mailservername; ?>'/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">郵件交換機就是 MX 標誌的內容，一般就是設定為妳的領域名稱即可。未來寄信的 email 為 <span class="text-danger">帳號@<?php echo $mailservername; ?></span>。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">Mail 主機 IP：</label>
                            <div class="col-lg-4">
                                <input type="text" name="mailserverip" class="form-control" value='<?php echo $mailserverip; ?>'/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">妳的伺服器若有 Mail 功能，這個設定就重要了。</p>
                            </div>
                        </div>
                        <div class="col-lg-offset-1 col-lg-10 well">
                            上述的設定已經有調整過，一般來說，都可以符合一般的主機名稱設定值。
                            所以，除非您知道您設定的項目所代表的意義，否則上述的設定可以不要修訂，保留設定值即可符合大部分的 DNS 設定了。
                            底下的設定則依據您的需求來設定主機名稱對應的 IP 即可。
                        </div>
                        <div class="col-lg-offset-1 col-lg-10">
                            <h4 class="page-header">新增其他的主機名稱與 IP 對應的設定值 (一次可新增3個，若超過，可於建立後於修改的畫面中新增)</h4>
                            <div class="text-center">
                                <p>主機名稱設定值--&gt;IP對應設定值</p>
                                <?php   for ( $i=1; $i<=3; $i++ ) { ?>
                                    <div class="form-group form-inline">
                                        <input name="sethostname[]" class="form-control" placeholder="主機名稱設定值"/>.<?php echo $zonename; ?>--&gt;
                                        <input name="setip[]" class="form-control" placeholder="IP對應設定值"/>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <hr>
                        <div class="col-lg-offset-5 col-lg-2">
                            <input type="hidden" name="action" value="yes" />
                            <button type="submit" class="btn btn-primary btn-block">開始建立</button>
                        </div>
                        <script>
                            function check(f) {

                                // 先看資源名稱是否符合正確！
                                var flag=true;
                                var re = /^[0-9.]+$/;
                                if (!re.test(f.dnsserverip.value)) {
                                    alert("IP 只能是數字與 . 的組合啊！！");
                                    f.dnsserverip.focus();
                                    flag=false;
                                    return flag;
                                }
                                if (!re.test(f.mailserverip.value)) {
                                    alert("IP 只能是數字與 . 的組合啊！！");
                                    f.mailserverip.focus();
                                    flag=false;
                                    return flag;
                                }

                                return confirm("是否確定上傳上述資料？");
                            }

                            function nothing() {
                                return false ;
                            }
                        </script>
                    <?php } ?>
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
