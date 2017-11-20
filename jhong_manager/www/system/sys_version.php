<?php 
	$buttom      = "system";
	$check_admin = "user";
	$jhtitle     ="系統資訊圖表";
	include("../include/header.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Linux  - 系統資訊</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <table border="0" align="center">
                        <tr>
                            <td><b>CPU：</b></td>
                            <td><a target="_blank" href="../<?php echo $mrtg; ?>/cpu.html"><img src="<?php echo  $mrtg ;?>/cpu-day.png"></a></td>
                        </tr>
                        <tr>
                            <td><b>網路：</b></td>
                            <td><a target="_blank" href="../<?php echo $mrtg; ?>/net.html"><img src="<?php echo  $mrtg ;?>/net-day.png"></a></td>
                        </tr>
                        <tr>
                            <td><b>硬碟：</b></td>
                            <td><a target="_blank" href="../<?php echo $mrtg; ?>/disk.html"><img src="<?php echo  $mrtg ;?>/disk-day.png"></a></td>
                        </tr>
                        <tr>
                        <td><b>RAM：</b></td>
                            <td><a target="_blank" href="../<?php echo $mrtg; ?>/memory.html"><img src="<?php echo  $mrtg ;?>/memory-day.png"></a></td>
                        </tr>
                    </table>
                </div>
                <!-- /.panel-body  -->
            </div>
            <!-- /.panel  -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->

<?php   include("../include/footer.php"); ?>
