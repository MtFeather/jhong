<?php 
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "防火牆觀察與設定";
	include("../include/header.php");

	$log = exec ( "sudo bash -c \" [ ! -f /jhong/bin/iptables.trustip ] && touch /jhong/bin/iptables.trustip; [ ! -f /jhong/bin/iptables.tcpservice ] && touch /jhong/bin/iptables.tcpservice ; [ ! -f /jhong/bin/iptables.udpservice ] && touch /jhong/bin/iptables.udpservice ; [ ! -f /jhong/bin/iptables.tcpport ] && touch /jhong/bin/iptables.tcpport; [ ! -f /jhong/bin/iptables.udpport ] && touch /jhong/bin/iptables.udpport; [ ! -f /jhong/bin/iptables.cracker ] && touch /jhong/bin/iptables.cracker \" " );

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "go" ) {
			$getcracker 	= $_REQUEST['cracker'];
			$gettrustip 	= $_REQUEST['trustip'];
			$gettcpservice 	= $_REQUEST['tcpservice'];
			$gettcpport 	= $_REQUEST['tcpport'];
			$getudpport 	= $_REQUEST['udpport'];

			$mesg = "";
			foreach ($getcracker as $temp ) {
				if ( trim($temp) != "" ) {
					if ( $mesg == "" ) $mesg = trim($temp); else $mesg = "${mesg}\n${temp}";
				}
			}
			$log = exec ( "sudo bash -c \" echo \\\"$mesg\\\" > /jhong/bin/iptables.cracker \"" );

			$mesg = "";
			foreach ($gettrustip as $temp ) {
				if ( trim($temp) != "" ) {
					if ( $mesg == "" ) $mesg = trim($temp); else $mesg = "${mesg}\n${temp}";
				}
			}
			$log = exec ( "sudo bash -c \" echo \\\"$mesg\\\" > /jhong/bin/iptables.trustip \"" );

			$mesg  = "";
			$mesg2 = "";
			foreach ($gettcpservice as $temp ) {
				if ( trim($temp) != "" ) {
					if ( $temp == '53' ) $mesg2 = "53";
					if ( $mesg == "" ) $mesg = trim($temp); else $mesg = "${mesg}\n${temp}";
					if ( $temp == '139' ) $mesg = "${mesg}\n445";
					if ( $temp == '993' ) $mesg = "${mesg}\n995";
				}
			}
			$log = exec ( "sudo bash -c \" echo \\\"$mesg\\\" > /jhong/bin/iptables.tcpservice \"" );
			$log = exec ( "sudo bash -c \" echo \\\"$mesg2\\\" > /jhong/bin/iptables.udpservice \"" );

			$mesg = "";
			foreach ($gettcpport as $temp ) {
				if ( trim($temp) != "" ) {
					if ( $mesg == "" ) $mesg = trim($temp); else $mesg = "${mesg}\n${temp}";
				}
			}
			$log = exec ( "sudo bash -c \" echo \\\"$mesg\\\" > /jhong/bin/iptables.tcpport \"" );

			$mesg = "";
			foreach ($getudpport as $temp ) {
				if ( trim($temp) != "" ) {
					if ( $mesg == "" ) $mesg = trim($temp); else $mesg = "${mesg}\n${temp}";
				}
			}
			$log = exec ( "sudo bash -c \" echo \\\"$mesg\\\" > /jhong/bin/iptables.udpport \"" );

			$log = exec ( "sudo sh /jhong/bin/iptables.sh " );
		}
	}

	$the_service[0] = '網頁伺服器 (http)'; 		$the_sport[0] = '80';
	$the_service[1] = '加密網頁伺服器 (https)'; 	$the_sport[1] = '443';
	$the_service[2] = '網芳檔案伺服器 (samba)'; 	$the_sport[2] = '139';
	$the_service[3] = 'FTP 檔案伺服器 (FTP)'; 	$the_sport[3] = '21';
	$the_service[4] = '領域伺服器 (DNS)'; 		$the_sport[4] = '53';
	$the_service[5] = '電子郵件 (MTA)'; 		$the_sport[5] = '25';
	$the_service[6] = '電子郵件 (POP3s/IMAPs)';	$the_sport[6] = '993';

	$templine = shell_exec ( "echo \$(cat /jhong/bin/iptables.cracker) " );
	$templine = str_replace ("\r\n","",trim($templine));
	$temparray = explode( " ", $templine);
	$i = 0;
	foreach ( $temparray as $temp ) {
		if ( trim($temp) != "" ) {
			$cracker[$i] = trim($temp);
			$i = $i + 1;
		}
	}
	unset ( $templine ); unset ($temparray); unset($temp); unset($i);

	$templine = shell_exec ( "echo \$(cat /jhong/bin/iptables.trustip) " );
	$templine = str_replace ("\r\n","",trim($templine));
	$temparray = explode( " ", $templine);
	$i = 0;
	foreach ( $temparray as $temp ) {
		if ( trim($temp) != "" ) {
			$trustip[$i] = trim($temp);
			$i = $i + 1;
		}
	}
	unset ( $templine ); unset ($temparray); unset($temp); unset($i);

	$templine = shell_exec ( "echo \$(cat /jhong/bin/iptables.tcpservice) " );
	$templine = str_replace ("\r\n","",trim($templine));
	$temparray = explode( " ", $templine);
	$i = 0;
	foreach ( $temparray as $temp ) {
		if ( trim($temp) != "" ) {
			$tcpservice[$i] = trim($temp);
			$i = $i + 1;
		}
	}
	unset ( $templine ); unset ($temparray); unset($temp); unset($i);

	$templine = shell_exec ( "echo \$(cat /jhong/bin/iptables.tcpport) " );
	$templine = str_replace ("\r\n","",trim($templine));
	$temparray = explode( " ", $templine);
	$i = 0;
	foreach ( $temparray as $temp ) {
		if ( trim($temp) != "" ) {
			$tcpport[$i] = trim($temp);
			$i = $i + 1;
		}
	}
	unset ( $templine ); unset ($temparray); unset($temp); unset($i);

	$templine = shell_exec ( "echo \$(cat /jhong/bin/iptables.udpport) " );
	$templine = str_replace ("\r\n","",trim($templine));
	$temparray = explode( " ", $templine);
	$i = 0;
	foreach ( $temparray as $temp ) {
		if ( trim($temp) != "" ) {
			$udpport[$i] = trim($temp);
			$i = $i + 1;
		}
	}
	unset ( $templine ); unset ($temparray); unset($temp); unset($i);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">系統防火牆觀察與設定</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <form  action="sys_firewall.php" OnSubmit="return checkme(this)" >
                <div class="panel-group">
                    <div class="panel panel-default">
                        <div class="panel-heading">黑名單網域 (IP or Network/netmask)</div>
                        <div class="panel-body">
                            <div class='form-group'>
                            <?php
                                foreach ( $cracker as $temp ) {
                                    echo "<div class='checkbox col-lg-12'>
                                              <label><input type='checkbox' name='cracker[]' checked='checked' value='$temp'>$temp</label>
                                          </div>";
                                }
                            ?>
                            </div>
                            <?php
                                for ($i=1; $i<=3; $i++ ) {
                                    echo "<div class='form-group'>
                                              <div class='col-lg-4'>
                                                  <input type='text' class='form-control' name='cracker[]'/>
                                              </div>
                                              <div class='col-lg-8'>
                                                  <p class='form-control-static'>(ex> 192.168.1.1 or 192.168.1.0/24)</p>
                                              </div>
                                          </div>";
                                }
                            ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">信任用戶 (IP or Network/netmask)</div>
                        <div class="panel-body">
                            <div class='form-group'>
                            <?php
                                foreach ( $trustip as $temp ) {
                                    echo "<div class='checkbox col-lg-12'>
                                              <label><input type='checkbox' name='trustip[]' checked='checked' value='$temp' />$temp</label>
                                          </div>";
                                } 
                            ?>
                            </div>
                            <?php
                                for ($i=1; $i<=3; $i++ ) {
                                    echo "<div class='form-group'>
                                              <div class='col-lg-4'>
                                                  <input type='text' class='form-control' name='trustip[]'/>
                                              </div>
                                              <div class='col-lg-8'>
                                                  <p class='form-control-static'>(ex> 192.168.1.1 or 192.168.1.0/24)</p>
                                              </div>
                                          </div>";
                                }
                            ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">信任服務 (Services) (Samba 最好不要對 Internet 開放)</div>
                        <div class="panel-body">
                            <div class='form-group'>
                                <?php
                                    for ( $i=0; $i<=6; $i++) {
                                        $checked = "";
                                        foreach ( $tcpservice as $temp ) {
                                            if ( $the_sport[$i] == $temp )  $checked = "checked='checked'";
                                        }
                                        echo "<div class='checkbox col-lg-12'>
                                                  <label><input type='checkbox' name='tcpservice[]' $checked value='$the_sport[$i]'/>$the_service[$i]</label>
                                              </div>";
                                    }
                                ?>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">信任 TCP 埠口 (tcp port)</div>
                        <div class="panel-body">
                            <div class='form-group'>
                                <?php
                                    foreach ( $tcpport as $temp ) {
                                        echo "<div class='checkbox col-lg-12'>
                                                  <label><input type='checkbox' name='tcpport[]' checked='checked' value='$temp' />$temp</label>
                                              </div>";
                                    }
                                ?>
                            </div>
                                <?php
                                    for ($i=1; $i<=3; $i++ ) {
                                        echo "<div class='form-group'>
                                                  <div class='col-lg-4'>
                                                      <input type='text' class='form-control' name='tcpport[]'/>
                                                  </div>
                                                  <div class='col-lg-8'>
                                                      <p class='form-control-static'>(ex> 3306, 3128..)</p>
                                                  </div>
                                              </div>";
                                    }
                                ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">信任 UDP 埠口 (udp port)</div>
                        <div class="panel-body">
                            <div class='form-group'>
                                <?php
                                    foreach ( $udpport as $temp ) {
                                        echo "<div class='checkbox col-lg-12'>
                                                  <label><input type='checkbox' name='udpport[]' checked='checked' value='$temp' />$temp</label>
                                              </div>";
                                    }
                                ?>
                            </div>
                                <?php
                                    for ($i=1; $i<=3; $i++ ) {
                                        echo "<div class='form-group'>
                                                  <div class='col-lg-4'>
                                                      <input type='text' class='form-control' name='udpport[]'/>
                                                  </div>
                                                  <div class='col-lg-8'>
                                                      <p class='form-control-static'>(ex> 3306, 3128..)</p>
                                                  </div>
                                              </div>";
                                    }
                                ?>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-body text-center">
                            <input type="hidden" name="action" value="go" />
                            <button type="submit" class="btn btn-primary">更新防火牆</button>
                            <a href="sys_firewall_look.php" class="btn btn-primary">查看防火牆規則</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
                <!-- /.panel-group  -->
            </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checkme(f) {
		// 先宣告會用到的各項變數
		var re  = /^[1-9][0-9.\/]+$/;
		var re2 = /^[1-9][0-9]+$/;
		var trustip = new Array();
		var tcpport = new Array();
		var udpport = new Array();
		var i = 0;
		trustip = document.getElementsByName("trustip[]");
		tcpport = document.getElementsByName("tcpport[]");
		udpport = document.getElementsByName("udpport[]");

		for ( i = 0; i <= trustip.length - 1; i++ ) {
			if ( trustip[i].value != "" ) {
				if ( !re.test(trustip[i].value)) {
					alert("IP 的格式不太對勁！");
					return false;
				}
			}
		}

		for ( i = 0; i <= tcpport.length - 1; i++ ) {
			if ( tcpport[i].value != "" ) {
				if ( !re2.test(tcpport[i].value)) {
					alert("TCP port 有非數字存在");
					return false;
				}
			}
		}

		for ( i = 0; i <= udpport.length - 1; i++ ) {
			if ( udpport[i].value != "" ) {
				if ( !re2.test(udpport[i].value)) {
					alert("UDP port 有非數字存在");
					return false;
				}
			}
		}

		return confirm("是否確定要修改防火牆規則了呢？");
	}

</script>

<?php   include("../include/footer.php"); ?>
