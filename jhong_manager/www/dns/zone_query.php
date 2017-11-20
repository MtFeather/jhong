<?php	
	$buttom		= "dns_server";
	$check_admin 	= "admin";
	$jhtitle     	= "查詢網域是否查詢到任何資料";
	include("../include/header.php");
	include("./dns_function.php");

	if ( isset ( $_REQUEST['myhostname'] ) ) 
		$myhostname = $_REQUEST['myhostname'];
	else
		$myhostname = '';

	if ( isset ( $_REQUEST['mytype'] ) )  {
		$mytype = $_REQUEST['mytype'];
		$mytype_option = "-t $mytype" ;
	} else {
		$mytype = '';
		$mytype_option = '';
	}

	if ( $myhostname != '' ) {
		$mymsg = shell_exec ( "dig $mytype_option $myhostname @127.0.0.1" );
	} else {
		$mymsg = '';
	}
	//echo "$myhostname $mytype";
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">DNS 網域查詢</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-body well-sm text-center">
                        <?php echo "<h4>$myhostname $mytype</h4>"; ?>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">查詢 DNS 結果</div>
                    <div class="panel-body text-center">
                       <form class="form-inline" method="post" name="form" action="<?php echo "$web/dns/zone_query.php" ;?>" >
                           <div class="form-group">
                               <label>主機名稱：</label>
                               <input type="text" class="form-control" name="myhostname" value="<?php echo $myhostname; ?>"/>
                           </div>
                           <div class="form-group">
                               <label>查詢類型：</label>
                               <select class="form-control" name="mytype" >
                               <?php
                                   $temp1[1]="a";                  $temp1[2]="ns";                 $temp1[3]="mx";
                                   $temp2[1]="主機 IP (A)";        $temp2[2]="伺服器 IP (NS)";     $temp2[3]="郵件設定 (MX)";
                                   for ( $i=1; $i<=3; $i++ ) {
                                       echo "<option value='$temp1[$i]'";
                                       if ( $mytype == $temp1[$i] ) echo "selected='selected'";
                                       echo ">$temp2[$i]</option>";
                                   }
                               ?>
                               </select>
                               <button type="submit" class="btn btn-primary">查詢</button>
                           </div>
                       </form>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <?php if ( $mymsg != '' ) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">查詢 DNS 結果</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;">
                            <?php echo $mymsg; ?>
                        </pre>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <?php } ?>
            </div>
            <!-- /.panel-group -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->	

<?php   include("../include/footer.php"); ?>
