<?php
	$check_admin = "user";
	include ("../include/header.php");
		$index = "..";
		$back   = ".";
		$action	 = $_POST['action'];
		$title   = $_POST['title'];
		$content = $_POST['content'];
		$sql_uid = "select uid from j_user where username = '$sql_username'";
		$result  = mysql_query($sql_uid);
		$row 	 = @mysql_fetch_row($result);
		$uid	 = $row[0];

######################################################發佈訊息############################################
	if($action == 1 ){
		if ( $title == "" ){	
			echo "<div class='well well-lg' style='text-align: center;'>標題欄沒有輸入!〈3秒後將返回首頁〉</div>";
                        echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/index.php>";
		} elseif ( $content == "" ){
                        echo "<div class='well well-lg' style='text-align: center;'>內容欄沒有輸入!〈3秒後將返回首頁〉</div>";
                        echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/index.php>";
		} else {
			$sql ="insert into j_news (uid,title,content,j_news_time,j_news_update_time) values ('$uid','$title','$content',now(),now())";
			if(mysql_query($sql)){
				echo "<script>location.href='$web/index.php';</script>";
			} else {
		                echo "<div class='well well-lg' style='text-align: center;'>留言失敗，請重新輸入";
				echo "<a href='$web/index.php'>回首頁</a></div>";
	                }	
		}
	}
	
######################################################新增留言############################################
	if($action == 2 ) {
		$news_num = $_GET['id'];
		if ($content == "" ){
			 echo "<script>alert('內容不能空白唷!!')</script>";
                         echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/sql/show_post.php?nid=$news_num>";
		} else {
			$sql ="insert into j_news_reply (news_num,uid,content,j_news_reply_time,j_news_reply_update_time) values ('$news_num','$uid','$content',now(),now())";
                	if(mysql_query($sql)){
				echo "<script>location.href='$web/sql/show_post.php?nid=$news_num';</script>";
			} else {
                        	echo "<div class='well well-lg' style='text-align: center;'>留言失敗，請重新輸入";
                                echo "<a href='index.php'>回首頁</a></div>";
                        }
		}
	}
######################################################修改訊息############################################
	if($action == 3){
			$nid = $_GET['id'];
			if ( $title == "" ){
                                        echo "<div class='well well-lg' style='text-align: center;'>標題欄沒有輸入!〈3秒後將返回首頁〉</div>";
                        }
                        elseif ( $content == "" ){
                                 	echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/sql/show_post.php?nid=$nid>";
                                        echo "<div class='well well-lg' style='text-align: center;'>內容欄沒有輸入!〈3秒後將返回首頁〉</div>";
                        }
                        else{

		$sql ="update j_news set title='$title', content='$content', j_news_update_time=NOW() where num='$nid'";
		
		if(mysql_query($sql)){

					echo "<div class='well well-lg' style='text-align: center;'>修改成功〈3秒後將返回上一頁〉</div>";
					 echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/sql/show_post.php?nid=$nid>";
		}else {
					echo "<div class='well well-lg' style='text-align: center;'>修改失敗</div>";
					echo "$sql";
			}
}
}
######################################################修改留言############################################
	if($action == 4){
		$num = $_GET['id'];
		$nid = $_GET['nid'];
		$sql ="update j_news_reply set content='$content', j_news_reply_update_time=NOW() where num='$num'";
		if(mysql_query($sql)){
			$sql_time="update j_news set j_news_update_time=NOW() where num='$nid'";
			if(mysql_query($sql_time)){
			 	echo "<div class='well well-lg' style='text-align: center;'>修改成功〈3秒後將返回上一頁〉</div>";
                         	echo "<meta http-equiv=REFRESH CONTENT=3;url=$web/sql/show_post.php?nid=$nid>";
			}
		
		}else {
			 echo "<div class='well well-lg' style='text-align: center;'>修改失敗</div>";
}
}
	
######################################################刪除留言############################################
	if($action == 5){
		$num = $_GET['id'];
		$nid = $_GET['nid'];
		$sql ="delete from  j_news_reply where num='$num'";
		if(mysql_query($sql)){
			echo "<script>location.href='$web/sql/show_post.php?nid=$nid';</script>";
		}else {
			echo "<div class='well well-lg' style='text-align: center;'>刪除失敗";
			echo "$sql</div>";
		}
	}

######################################################刪除留言############################################
	if($action == 6){
		$num = $_GET['id'];
		$nid = $_GET['nid'];
		$sql ="delete from j_news_reply where news_num='$nid'";
		if(mysql_query($sql)){
			$sql_news="delete from j_news where num='$nid'";
			if(mysql_query($sql_news)){
				echo "<script>location.href='$web/index.php';</script>";
			}
		}else{
			echo "<div class='well well-lg' style='text-align: center;'>刪除失敗</div>";
		}
	}
?>
<?php   include("../include/footer.php"); ?>

