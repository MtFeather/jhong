<?php 
	$buttom      = "system";
	$check_admin = "user";
	$jhtitle     ="系統資訊";
	include("../include/header.php");
	$mytime = date("Y-m-d / H:i:s");
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
	            <table>
                    <?php
                        $sys_list = shell_exec("sudo sh $sdir/system/sys_version.sh");
                        echo $sys_list;
                    ?>
                    </table>

                    <table>
                        <tr>
                            <td style="width: 150px;"><b>系統現在時間</b></td>
                            <td><b>：</b> <?php echo $mytime ;  ?></td>
                        </tr>
                        <?php if ( $_SESSION['userlevel'] == "admin" ) { ?>
                   	<tr>
                            <td style='width: 150px;'><b>修改系統時間</b></td>
                            <td>
                                <b>：</b>
                         	<form method='post'  action='sys_mod_serverdate.php'  style='width: 200px; display: inline;'>
                                    <button type='submit' class='btn btn-primary btn-sm'>單次自動網路校時</button>
                                </form>
                           	<form method='post' action='sys_mod_date.php' style='width: 150px; display: inline;'>
                                    <button type='submit' class='btn btn-primary btn-sm'>手動輸入</button>
                                </form>
                            </td>
                        </tr>
                        <?php } ?>
                    </table>
                    <input type="hidden" name="gotodo" value="yes" />
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
