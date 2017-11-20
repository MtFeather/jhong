<?php
	$buttom      = "new";
	$check_admin = "user";
	include ("../include/header.php");

	// Get the post messages
	$nid = $_GET['check'];
	$sql="select num,title,content from j_news where num=$nid";
	$result = mysql_query($sql,$link);
	while($row = mysql_fetch_row($result))	{
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">公佈欄消息 - 修改原始貼文</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal" name="form" action="proc.php?id=<?php echo $nid ;?>" method="post">
                <div class="form-group">
                    <label class="control-label col-lg-1">標題:</label>
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="title" id="username" value="<?php echo $row[1];?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-lg-1">內文:</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" Rows="10" name="content" ><?php echo $row[2];?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-lg-offset-5 col-lg-2">
                        <input type="hidden" name="action" value="3">
                        <button type="submit" class="btn btn-primary btn-block">送出</button>
                    </div>
                </div>
	    </form>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<?php
	}
?>
<?php  include ("../include/footer.php"); ?>
