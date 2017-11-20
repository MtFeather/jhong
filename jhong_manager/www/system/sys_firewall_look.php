<?php 
	$buttom      = "system";
	$check_admin = "admin";
	$jhtitle     = "防火牆觀察與設定";
	include("../include/header.php");

	$thismsg = shell_exec ( "sudo iptables-save | grep -v 120.114.142.128/25 | grep -v 120.114.140.0/25 | grep -v 59.125.213.145/32 | grep -v 122.117.222.8/32 |  grep -v 59.125.213.146/32 |  grep -v 59.125.213.147/32" );

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
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">防火牆規則一覽表</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;"><?php   echo $thismsg;  ?></pre>
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
<?php   include( "../include/footer.php" ); ?>
