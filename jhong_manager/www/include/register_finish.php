<?php session_start(); ?>
<meta charset="utf8" />
<?php
include("mysql_config.php");
$username = $_POST['id'];
$password = sha1($_POST['pw']);
$password2 = sha1($_POST['pw2']);
$all_name = $_POST['allname'];
$level = $_POST['level'];
$u_group = $_POST['ugroup'];
$email = $_POST['email'];
$tel = $_POST['telephone'];
$note = $_POST['other'];
//$mysqldate = 'current_timestamp';
$datetime = date ("Y-m-d H:i");
//$i = echo $datetime ;
$null = '';
//判斷帳號密碼是否為空值
//確認密碼輸入的正確性
if($username != null && $password != null && $password2 != null && $password == $password2)
{
        //新增資料進資料庫語法
        $sql = "insert into j_user values ('null', '$username', '$password', '$all_name', '$level', '$u_group', '$email', '$tel', '$note', '$datetime', 'null')";
        echo $sql;
        if(mysql_query($sql))
        {
                echo '新增成功!';
                echo '<meta http-equiv=REFRESH CONTENT=2;url=index.php>';
        }
        else
        {
                echo '新增失敗!';
                echo '<meta http-equiv=REFRESH CONTENT=2;url=index.php>';
        }
}
else
{
        echo '您無權限觀看此頁面!';
        echo '<meta http-equiv=REFRESH CONTENT=2;url=index.php>';
}
?>

