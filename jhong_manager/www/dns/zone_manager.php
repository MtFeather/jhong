<?php 
	$buttom      = "dns_server";
	$check_admin = "admin";
	$jhtitle     = "修改 DNS 的網域正解設定";
	include ( "../include/header.php" );
	include ( "./dns_function.php" );

	if ( isset ( $_REQUEST['zonename'] ) ) {
		$zonename = $_REQUEST['zonename'];
	} else {
		$zonename = "";
	}

	for ( $i=1; $i<=$dnsnumber; $i++ ) {
		if ( $dns_zonename[$i] == $zonename ) {
			$thisline = $i; 
			break;
		}
	}
	$zonefile = $dns_zonefile[$thisline];
	$ttl 	        = $dns_ttl[$thisline];
	$serial         = $dns_serial[$thisline];
	$soa            = $dns_soa[$thisline];
	$dnsservername  = $dns_ns[$thisline];
	$dnsserverip    = $dns_nsip[$thisline];
	$mailservername = $dns_mx[$thisline];
	$mailserverip   = $dns_mxip[$thisline];
	if ( count( $dns_hostname[$thisline] ) > 0 ) {
		for ( $i=1; $i<=count( $dns_hostname[$thisline] ); $i++ ) {
			$old_hostname[$i] = $dns_hostname[$thisline][$i];
			$old_hostip[$i] = $dns_hostip[$thisline][$i];
		}
	}
	$serialnow = date("Ymd");
	$serialnu = (int)substr($serial,8,2) + 1 ;
	if ( $serialnu == 100 ) $serialnu = 1;
	if ( $serialnu < 10 ) $serial = $serialnow . "0" . $serialnu; else $serial = $serialnow . $serialnu;

	/*echo "$zonefile|$ttl|$serial|$soa|$dnsservername|$dnsserverip|$mailservername|$mailserverip|hostname=";
	foreach ($old_hostname as $temp) echo "$temp,";
	echo "|hostip=";
	foreach ($old_hostip as $temp) echo "$temp,";*/

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "yes" ) {

			//echo "$zonename|$ttl|$serial|$dnsservername|$dnsserverip|$mailservername|$mailserverip <br />";
			$sethostname    = $_REQUEST['sethostname'];
			$setip          = $_REQUEST['setip'];
			$ttl            = $_REQUEST['ttl'];
			$serial         = $_REQUEST['serial'];
			$dnsservername  = $_REQUEST['dnsservername'];
			$dnsserverip    = $_REQUEST['dnsserverip'];
			$mailservername = $_REQUEST['mailservername'];
			$mailserverip   = $_REQUEST['mailserverip'];
			$oldhost	= $_REQUEST['oldhost'];
			$getoldhostnu = 0;
			foreach ( $oldhost as $temp ) {
				$getoldhostnu = $getoldhostnu + 1; 
				$temparray = explode ( ";", $temp );
				$getoldhost[$getoldhostnu] = trim($temparray[0]);
				$getoldip[$getoldhostnu]   = trim($temparray[1]);
			}

			$mymesg = "";

			if ( $getoldhostnu > 0 ) {
				for ( $i=1; $i<=$getoldhostnu; $i++ ) {
					$mymesg = $mymesg . "$getoldhost[$i]	IN A	$getoldip[$i]\n";
				}
			}
			for ( $i=0; $i<=2; $i++ ) {
				//echo "$sethostname[$i]--$setip[$i]|";
				if ( $sethostname[$i] != "" ) $mymesg = $mymesg . "$sethostname[$i]	IN A	$setip[$i]\n";
			}
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
            <h1 class="page-header">修改 DNS 的正解領域設定</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form class="form-horizontal" method="post" name="addsambaform" action="<?php echo $web ;?>/dns/zone_manager.php" >
                <div class="panel panel-default">
                    <div class="panel-heading">修改 DNS 的正解設定</div>
                    <div class="panel-body">
                    <?php   if ( $zonename == "" ) { ?>
                        <div class="form-group">
                            <label class="control-label col-lg-3">正解的網域名稱：</label>
                            <div class="col-lg-4">
                                <select name="zonename" class="form-control">
                                    <?php
                                        if ( $dnsnumber > 0 ) {
                                            for ( $i=1; $i<=$dnsnumber; $i++ ) {
                                                echo "<option value='$dns_zonename[$i]'>$dns_zonename[$i]</option>";
                                            }
                                        }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-5">
                                <button type="submit" class="btn btn-primary">按此開始修改</button>
                            </div>
                        </div>
                    <?php   } else { ?>
                        <div class="form-group">
                            <label class="control-label col-lg-3">正解的網域名稱：</label>
                            <div class="col-lg-4">
                                <input type="hidden" name="zonename" value="<?php echo $zonename; ?>"/>
                                <input type="text" class="form-control" value="<?php echo $zonename; ?>" disabled/> 
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
                                <span class="form-control-static">你的紀錄資訊可以存在其他 DNS 伺服器暫存器多久之意。</span><br/>
                                <span class="form-control-static">變動中的伺服器可以調小一些，穩定的伺服器名稱可以久一些。</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">此設定之序號：</label>
                            <div class="col-lg-4">
                               <input type="text" class="form-control" name="serial" value="<?php echo $serial; ?>" readonly="readonly"/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">判斷此設定新舊的序號，自動新增，無須更動</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">DNS 主機名：</label>
                            <div class="col-lg-4 form-inline">
                               <input type="text" class="form-control" name="dnsservername" value="<?php echo $dnsservername; ?>"/>.<?php echo $zonename; ?>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">其實就是這部伺服器的主機名稱，用來管理 DNS 設定者，也是 NS 標誌。一般來說，使用本系統預設的設定值即可。同時與底下的 IP 要互相對應才行。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">DNS 主機 IP：</label>
                            <div class="col-lg-4">
                               <input type="text" class="form-control" name="dnsserverip" value="<?php echo $dnsserverip; ?>"/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">管理妳 DNS 領域的伺服器 IP ，就這部伺服器。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">郵件交換主機名：</label>
                            <div class="col-lg-4">
                               <input type="text" class="form-control" name="mailservername" value="<?php echo $mailservername; ?>"/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">郵件交換機就是 MX 標誌的內容，一般就是設定為妳的領域名稱即可。未來寄信的 email 為 <span class="text-danger">帳號@<?php echo $mailservername; ?></span>。</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-3">Mail 主機 IP：</label>
                            <div class="col-lg-4">
                               <input type="text" class="form-control" name="mailserverip" value="<?php echo $mailserverip; ?>"/>
                            </div>
                            <div class="col-lg-5">
                                <p class="form-control-static">妳的伺服器若有 Mail 功能，這個設定就重要了。</p>
                            </div>
                        </div>
                        <div class="col-lg-offset-1 col-lg-10">
                            <h4 class="page-header">刪除既有的設定值 (取消勾選擇是刪除喔！)</h4>
                            <?php if ( count($old_hostname) > 0 ) {
                                for ( $i=1; $i<=count($old_hostname); $i++ ) {
                            ?>
                                <div class="checkbox"><label><input type="checkbox" checked='checked' name="oldhost[]"  value='<?php echo "$old_hostname[$i];$old_hostip[$i]"; ?>' />
                                <?php echo "$old_hostname[$i].$zonename ($old_hostip[$i])";  ?></label></div>
                                <?php   }
                            } ?>
                        </div>
                        <div class="col-lg-offset-1 col-lg-10">
                            <h4 class="page-header">新增其他的主機名稱與 IP 對應的設定值 (一次可新增 3 個)</h4>
                            <div class="text-center">
                                <p>主機名稱設定值--&gt;IP對應設定值</p>
                                <?php for ( $i=1; $i<=3; $i++ ) { ?>
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
                            <button type="submit" class="btn btn-primary btn-block">開始訂正</button>
                        </div>
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
<?php   include( "../include/footer.php" ); ?>
