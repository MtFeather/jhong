<?php	
	$buttom		= "dns_server";
	$check_admin 	= "admin";
	$jhtitle     	= "網域列表與管理";
	include("../include/header.php");
	include("./dns_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "trust" ) {
			$oldtrust = $_REQUEST['oldtrust'];
			$mytrust  = $_REQUEST['mytrust'];
			$mymsg = "";
			foreach ( $oldtrust as $temp ) {
				$temp = str_replace ( "/", "\\/", $temp );
				if ( $mymsg == "" ) $mymsg = "${temp};" ; else $mymsg = "${mymsg} ${temp};";
			}
			if ( trim($mytrust) != "" ) {
				$mytrust = str_replace ( "/", "\\/", $mytrust );
				if ( $mymsg == "" ) $mymsg = "${mytrust};" ; else $mymsg = "${mymsg} ${mytrust};";
			}
			$newtrust = "	acl trustnetwork \{ $mymsg \};";
			//echo  "sudo sed -i 's/^[[:space:]]*acl trust.*$/$newtrust/g' /etc/named.conf "; die;
			$log = exec ( "sudo sed -i 's/^[[:space:]]*acl trust.*$/\\tacl trustnetwork \\{ $mymsg \\};/g' /etc/named.conf 2>&1" );
                        // 最終就得要重新啟動 dns 囉！
                        $log = exec ( "sudo systemctl restart named-chroot" );
                        echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/dns/zone_show.php';</script>";
                        die;
		}

		if ( $_REQUEST['action'] == "delete" ) {
			$zonename = $_REQUEST['zonename'];
			if ( $dnsnumber > 0 ) {
				for ( $i=1; $i<=$dnsnumber; $i++ ) {
					if ( $dns_zonename[$i] == $zonename ) { $thisdnsnumber = $i; break; }
				}
			}
			$log = shell_exec ( "line=\$( grep -n '^zone \\\"$dns_zonename[$thisdnsnumber]\\\"' /etc/named/named.jhong.conf | cut -d ':' -f1); sudo sed -i \"\$line d\" /etc/named/named.jhong.conf" );

			$log = exec ( "sudo rm $dns_zonefile[$thisdnsnumber]" );

                        // 最終就得要重新啟動 dns 囉！
                        $log = exec ( "sudo systemctl restart named-chroot" );
                        echo "<script>alert('刪除完畢，請檢查列表資訊'); location.href='$web/dns/zone_show.php';</script>";
                        die;
		}
	}
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">DNS 網域列表與管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <form method="post" OnSubmit="return checktrust(this)">
                    <div class="panel panel-default">
                        <div class="panel-heading">目前的信任用戶 (可以透過本 DNS 伺服器查詢其他主機名稱的功能)</div>
                        <div class="panel-body">
                            <?php   
                                if ( count($trustnetwork) > 0 ) {
                                    foreach ( $trustnetwork as $temp ) {
                                        if ( $temp == "localhost" ) $readonly = "disabled"; else $readonly = '';
                                        echo "<label class='checkbox-inline'><input name='oldtrust[]' type='checkbox' checked='checked' value='$temp' $readonly />";
                                        echo " $temp</label>";
                                    }
                                }
                            ?>    
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-body form-inline text-center">
                            <div class="form-group">
                                <label>新增信任用戶：</label>
                                <input type="text" name="mytrust" class="form-control"/>
                                <p class="form-control-static">(ex&gt; 192.168.1.0/24)</p>
                                <input type="hidden" name="action" value="trust" />
                                <button type="submit" class="btn btn-primary">開始修改</button>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </form>
            </div>
            <!-- /.panel-group  -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>領域名稱</th>
                            <th>內含主機名的設定</th>
                            <th>管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if ( $dnsnumber > 0 ) {
                                for ( $i=1; $i<=$dnsnumber; $i++ ) {
                                    echo "<tr><td><a href='zone_query.php?myhostname=$dns_zonename[$i]&mytype=ns'
                                          class='btn btn-outline btn-primary'>$dns_zonename[$i]</a></td>";
                                    echo "<td><a href='zone_query.php?myhostname=$dns_ns[$i].$dns_zonename[$i]'
                                          class='btn btn-outline btn-primary'>$dns_ns[$i].$dns_zonename[$i] ($dns_nsip[$i])</a><br />";
                                    echo "<a href='zone_query.php?myhostname=$dns_mx[$i]' 
                                          class='btn btn-outline btn-primary'>$dns_mx[$i] ($dns_mxip[$i]) </a><br />";
                                    for ( $j=1; $j<=count($dns_hostname[$i]); $j++ ) {
                                        $temphost[$j] = $dns_hostname[$i][$j];
                                        $temphostip[$j] = $dns_hostip[$i][$j];
                                    }
                                    for ( $j=1; $j<=count($dns_hostname[$i]); $j++ ) {
                                        echo "<a href='zone_query.php?myhostname=$temphost[$j].$dns_zonename[$i]' class='btn btn-outline btn-primary'
                                              >$temphost[$j].$dns_zonename[$i] ($temphostip[$j])</a><br />";
                                    }
                                    echo "</td><td>";
                                    echo "<a href='$web/dns/zone_show.php?action=delete&zonename=$dns_zonename[$i]'>";
                                    echo "<button class='btn btn-danger' OnClick='return delcheck()'/>刪除</button></a> ";
                                    echo "<a class='btn btn-success' href='zone_manager.php?zonename=$dns_zonename[$i]' />管理</a></td></tr>";
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checktrust(f) {
		return confirm("是否確定修改信任用戶呢？");
	}
	function delcheck() {
		return confirm("是否確定要刪除這個領域名稱的設定呢？");
	}
</script>

<?php   include("../include/footer.php"); ?>
