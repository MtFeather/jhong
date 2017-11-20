<?php 
	$buttom      = "samba_server";
	$check_admin = "admin";
	$jhtitle     = "samba 目前的掛載狀況";
	include ( "../include/header.php" );
	include ( "./samba_function.php" );

	$thismsg1 = shell_exec ( "sudo systemctl status smb -l " );
	$thismsg2 = shell_exec ( "sudo smbstatus " );

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">網芳現階段的系統與掛載狀況顯示</h1>
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
                <div class="panel panel-default">
                    <div class="panel-heading">掛載情況分析</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;"><?php   echo $thismsg2; ?></pre>
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
