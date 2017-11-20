<?php
	$buttom      = "new";
	$check_admin = "user";
	include ("../include/header.php");

	// Get the post ID and get the reply ID to process.
	$num = $_GET['id'];
	$nid = $_GET['nid'];
	$sql="select num,content from j_news_reply where num=$num";
	$result = mysql_query($sql,$link);
	while($row = mysql_fetch_row($result))	{
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">公佈欄消息 - 修改回覆文章</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <form name="form" action="proc.php?nid=<?php echo $nid ;?>&id=<?php echo $num ;?>" method="post">
                <div class="form-group col-lg-12">
                    <label>內文:</label>
                    <textarea class="form-control" style="width: 100%" Rows="10" name="content" ><?php echo $row[1];?></textarea>
                </div>
                <div class="form-group col-lg-offset-5 col-lg-2">
                    <input type='hidden' name='action' value='4' />
                    <button type="submit" class="btn btn-primary btn-block">送出</button>
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
