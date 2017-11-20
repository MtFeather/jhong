<?php 
	$buttom="new";
	$check_admin = "user";
	include ("../include/header.php");

	// Get the post id 
	$nid = $_GET['nid'];
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">公佈欄消息</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
<?php
	// Get the raw post data.
	$sql="select num,j_news.uid,title,content,all_name,j_news_time,username from j_news,j_user 
		where num=$nid && j_news.uid=j_user.uid";
	$result = mysql_query($sql,$link);

	// The raw post is in database
	while($row = mysql_fetch_row($result))	{
		$rawpost = "yes";
		$data = explode (" ",$row['5']);
?>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover">
                    <thead>
                        <tr>
                            <th colspan="2" class="col-lg-12 text-center">主題 : <?php echo $row[2]; ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="col-lg-2"><?php echo "<p>" . $row[4] . "</p><p>" . $data['0'] . "</p><p>" . $data['1'] . "</p>"; ?></td>
                            <td class="col-lg-10"><?php echo nl2br($row[3]); ?>
                                <?php if ( $sql_username == $row[6] || $_SESSION['userlevel'] == "admin" ) { ?>
                                <div class="clearfix"></div>
                                <form method="post" class="pull-right" style="display: inline;" action="show_modpost.php?check=<?php echo "$row[0]";?>">
                                    <button type="submit" name="smb" class="btn btn-outline btn-info">修改文章</button>
                                </form>
                                <form method="post" class="pull-right" style="display: inline;" action="proc.php?nid=<?php echo "$row[0]";?>">
                                    <button type="submit" name="smb" class="btn btn-outline btn-info" onClick="javascript:return myfunc()">刪除文章</button>
                                    <input type='hidden' name='action' value='6' />
                                </form>
                                <?php } ?>
                            </td>
                        </tr>
<?php   // If there have any reply, we will show the messages.
        $sql_count = "select count(*) from j_news_reply where news_num=$nid";
        $result_count = mysql_query($sql_count,$link);
        $count = mysql_result($result_count,0);
        if ( $count != 0 ) {
?>
                        <tr class="info"><th colspan="2" style="text-align:center;">留言回覆訊息</th></tr>
<?php   } ?>

<?php   // Get the reply messages and treate it.
        $sql_reply="select all_name,j_news.title,j_news_reply.content,j_news_reply_time,j_news_reply.num,username 
                from j_user,j_news,j_news_reply where j_user.uid=j_news_reply.uid 
                and j_news_reply.news_num=j_news.num and j_news_reply.news_num = $nid 
                order by j_news_reply_time " ;
        $result = mysql_query($sql_reply,$link);
        while($row_reply = mysql_fetch_row($result))  {
                $data_reply = explode (" ",$row_reply['3']);
?>
                        <tr>
                            <td class="col-lg-2"><?php echo "<p>" . $row_reply[0] . "</p><p>" . $data_reply['0'] . "</p><p>" . $data_reply['1'] . "</p>"; ?></td>
                            <td class="col-lg-10"><?php echo nl2br($row_reply[2]); ?>
                                <?php if ( $sql_username == $row_reply[5] || $_SESSION['userlevel'] == "admin" ) { ?>
                                <div class="clearfix"></div>
                                <form class="pull-right" style="display:inline;" method="post" action="show_modreply.php?nid=<?php echo $nid ;?>&id=<?php echo $row_reply[4] ;?>">
                                    <button type="submit" name="smb" class="btn btn-outline btn-info">修改留言</button>
                                    <input type='hidden' name='action' value='4' />
                                </form>
                                <form class="pull-right" style="display:inline;" method="post" action="proc.php?nid=<?php echo $nid; ?>&id=<?php echo $row_reply[4] ;?>">
                                    <button type="submit" class="btn btn-outline btn-info" onClick="javascript:return myfunc()">刪除留言</button>
                                    <input type='hidden' name='action' value='5' />
                                </form>
                               <?php } ?>
                            </td>
                        </tr>
<?php   } ?>
                        <tr>
                            <td colspan="2" style="text-align:center;">回覆此留言
                                <form name="form" action="proc.php?id=<?php echo $nid; ?>" method="post" OnSubmit="return checkreply()">
                                    <textarea class="form-control" Rows="5" name="content"></textarea>
                                    <div class="clearfix"></div>
                                    <button type='submit' name='button' class="btn btn-primary">送出</button>
                                    <input type='hidden' name='action' value='2' />
                                </form>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
<?php
        }       // No rawpost here.
        if ( ! isset ($rawpost) ) {
                        echo "<div class='well well-lg' style='text-align: center;'>並沒有這個貼文存在</div>";
        }
?>

        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
        function myfunc(){
        var msg ="你確定要刪除留言嗎?\n\n請確認!";
        if (confirm(msg)==true){
                        return true;
        }else{
                return false;
        }
}
	function checkreply() {
		return confirm("是否確定上傳訊息？");
	}
</script>
<?php include ("../include/footer.php");?>

