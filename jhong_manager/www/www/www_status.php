<?php	
	$buttom      = "apache";
	$check_admin = "admin";
	$jhtitle     = "WWW 伺服器";
	include ("../include/header.php");
	include ("./www_function.php");

	$mycheck  = shell_exec ( " sudo apachectl configtest 2>&1 " );
	$mystatus = shell_exec ( " sudo systemctl status httpd -l " );
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">WWW 伺服器的查看虛擬主機設定值</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">語法檢驗結果</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;"><?php echo $mycheck; ?></pre>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">掛載情況分析</div>
                    <div class="panel-body">
                        <pre style="border: 0; background-color: transparent;"><?php echo $mystatus; ?></pre>
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
<?php   include("../include/footer.php"); ?>
