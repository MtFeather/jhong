<?php     
	$buttom	     = "new";
	$check_admin = "user";
	$jhtitle     = "留言板";
	include("../include/header.php");
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">發佈訊息</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    留言版
                </div>
                <div class="panel-body">
                    <form  class="form-horizontal" method="post" action="proc.php" OnSubmit="return checkform()">
                        <div class="form-group">
                            <label class="control-label col-lg-1">標題：</label>
                            <div class="col-lg-6">
                                <input type="text" class="form-control" name="title" id="title"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-lg-1">內容：</label>
                            <div class="col-lg-11">
                                <textarea class="form-control" name="content" id="title" rows="15"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" name="action" value="1" />
                            <div class="col-lg-offset-5 col-lg-2">
                                <button type="submit" class="btn btn-primary btn-block">送出</button>
                            </div>
                        </div>
                    </form>
                    <!-- /.form  -->
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
<script>
	function checkform(){
		if ( news.title.value == "" ){
			alert("標題沒有填寫喔");
			return false;
		} else if ( news.content.value == '' ) {
			alert("內容空白是怎樣");
			return false;
		} else {
			var mycheck = confirm("是否確定要上傳此貼文");
			return mycheck;
		}
	}
</script>

<?php     include("../include/footer.php"); ?>
