<?php 
	$buttom      = "sql";
	$check_admin = "admin";
	$jhtitle     = "Mariadb 目前的狀況";
	include ( "../include/header.php" );

	$thismsg1 = shell_exec ( "sudo systemctl status mariadb -l " );

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">SQL 伺服器現階段的系統狀況顯示</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">系統狀態一覽表</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;"><?php   echo $thismsg1; ?></pre>
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
