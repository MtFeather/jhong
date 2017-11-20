<?php	
	$buttom="new";
	include("include/header.php"); 
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">訊息留言板</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
<?php	if ( $_SESSION['username'] != null ) {
    echo "<div class='row'>";
    echo "<div class='col-lg-12'>";
    echo "<a class='btn btn-default' href='$web/sql/news.php'>我要發佈訊息</a>";
    echo "</div></div>";
}?>
    <div class="row">
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th class="col-lg-8">標題</th>
                            <th class="col-lg-2">留言者</th>
                            <th class="col-lg-2">最新發佈或回應時間</th>
                        </tr>
                    </thead>
                    <tbody>
<?php
	// Get the news title and users.
	$sql = "select num,j_news.uid,title,content,all_name,j_news_time,j_news_update_time from j_news,j_user where j_news.uid=j_user.uid order by j_news_update_time DESC";
	$result = mysql_query($sql,$link);
	while($row = mysql_fetch_row($result)) {
		$data = explode (" ",$row['5']);
?>
                        <tr>
                            <td>
<?php 		if ( $_SESSION['username'] != null ){  ?>
                                <a  href='<?php echo $web . "/sql/show_post.php"; ?>?nid=<?php echo $row[0]; ?>'>
<?php		} ?>
		                 <?php echo $row[2]; ?></a></td>
                            <td><?php echo   $row['4'] ;?></td>
                            <td><?php echo   $data['0'] ; ?></td>
                        </tr>
<?php	}	?> 
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<?php	include("include/footer.php"); ?>
