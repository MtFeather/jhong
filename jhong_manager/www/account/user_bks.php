<?php
	$buttom		= "account";
	$check_admin 	= "admin";
	$jhtitle      	= "使用者凍結/解除凍結的方法 ";
	include("../include/header.php");

	// 加入凍結
	if ( isset ( $_REQUEST['addusergo'] ) ) {
		if ( $_REQUEST['addusergo'] == "yes" ) {
			$addusers = $_REQUEST['adduser'];
			foreach ( $addusers as $thisuser ) {
				$log = exec ( "sudo usermod -l  bk_$thisuser $thisuser; sudo usermod -L bk_$thisuser 2>&1" );
			}
		}
	}

	// 解除凍結
	if ( isset ( $_REQUEST['removeusergo'] ) ) {
		if ( $_REQUEST['removeusergo'] == "yes" ) {
			$removeusers = $_REQUEST['removeuser'];
			foreach ( $removeusers as $thisuser ) {
				$log = exec ( "sudo usermod -l  $thisuser bk_$thisuser; sudo  usermod -U $thisuser 2>&1 " );
			}
		}
	}
	include ("function.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">凍結/解凍結 使用者</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <div class="panel panel-default">
                    <div class="panel-heading">批次凍結用戶</div>
                    <div class="panel-body">
                        <form method="post" name="adduserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return checkadduser()">
                        <?php
                            $tempyes = 0;
                            $thisline = 0;
                            echo "<div class='row'><div class='col-lg-2'>";
                            for( $i=1; $i<=$usernumber; $i++ ) {
                                if ( $my_user_bk[$i] == "no" ) {
                                    $tempyes = $tempyes + 1;
                                    $thisline = $thisline + 1;
                                    echo "<div class='checkbox'><label><input type='checkbox' name='adduser[]' value='$my_username[$i]'/>$my_username[$i]</label></div>";
                                    if ( $thisline == 10 ) {
                                        echo "</div>";
                                        echo "<div class='col-lg-2'>";
                                        $thisline = 0;
                                    }
                                }
                            }
                            echo "</div></div>";
                            if ( $tempyes == 0 ) {
                                echo "無用戶";
                            }
                        ?>
                            <div class="row text-center">
                                <input type="hidden" name="group" value="<?php echo $group ?>" />
                                <input type="hidden" name="addusergo" value="yes" />
                                <button type="submit" class="btn btn-primary">開始凍結使用者</button>
                                <button type="button" class="btn btn-primary" onclick="adduseryes();">全勾選</button>
                                <button type="button" class="btn btn-primary" onclick="addusernot();">全取消</button>
                            </div>
                        </form>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
                <div class="panel panel-default">
                    <div class="panel-heading">批次解除凍結</div>
                    <div class="panel-body">
                        <form method="post" name="removeuserform" action="<?php echo $SERVER['PHP_SELF'];?>" OnSubmit="return checkremoveuser()">
                        <?php
                            $tempyes = 0;
                            $thisline = 0;
                            echo "<div class='row'><div class='col-lg-2'>";
                            for( $i=1; $i<=$usernumber; $i++ ) {
                                if ( $my_user_bk[$i] == "yes" ) {
                                    $tempyes = $tempyes + 1;
                                    $thisline = $thisline + 1;
                                    echo "<div class='checkbox'><label><input type='checkbox' name='removeuser[]' value='$my_username[$i]'/>";
                                    echo "<strong class='text-danger'>$my_username[$i]</strong></label></div>";
                                    if ( $thisline == 10 ) {
                                        echo "</div>";
                                        echo "<div class='col-lg-2'>";
                                        $thisline = 0;
                                    }
                                }
                            }
                            echo "</div></div>";
                            if ( $tempyes == 0 ) {
                                echo "無用戶";
                            }
                        ?>
                            <div class="row text-center">
                                <input type="hidden" name="group" value="<?php echo $group ?>" />
                                <input type="hidden" name="removeusergo" value="yes" />
                                <button type="submit" class="btn btn-primary">開始解除凍結</button>
                                <button type="button" class="btn btn-primary" onclick="removeuseryes();">全勾選</button>
                                <button type="button" class="btn btn-primary" onclick="removeusernot();">全取消</button>
                            </div>
                        </form>
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
<script>
	function checkadduser() {
		var $msgcheckadduser = confirm("確定將選擇的使用者凍結起來？？\n\n請確認");
		return $msgcheckadduser;
	}

	function adduseryes() {
		var checkadduseryes = document.getElementsByName("adduser[]");
		for(var i=0;i<checkadduseryes.length;i++){
			checkadduseryes[i].checked=true;
        	}
	}

	function addusernot() {
		var checkaddusernot = document.getElementsByName("adduser[]");
		for(var i=0;i<checkaddusernot.length;i++){
			checkaddusernot[i].checked=false;
        	}
	}
	function checkremoveuser() {
		var $msgcheckremoveuser = confirm("確定將選擇的使用者解除凍結？\n\n請確認");
		return $msgcheckremoveuser;
	}

	function removeuseryes() {
		var checkremoveuseryes = document.getElementsByName("removeuser[]");
		for(var i=0;i<checkremoveuseryes.length;i++){
			checkremoveuseryes[i].checked=true;
        	}
	}

	function removeusernot() {
		var checkremoveusernot = document.getElementsByName("removeuser[]");
		for(var i=0;i<checkremoveusernot.length;i++){
			checkremoveusernot[i].checked=false;
        	}
	}
</script>

<?php	include("../include/footer.php"); ?>
