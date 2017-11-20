<?php
        $www='/home/JHong/www';
        $web='/~JHong';
        $sdir='/home/JHong/www_scripts';
        $img='/~JHong/images';
        $sambapath='/home/JHong/www/samba/smb';
        if ( isset ( $_REQUEST['logincheck']) ) {
                $logincheck = $_REQUEST['logincheck'];
                if ( $logincheck == "yes" ) {
                        include ("${www}/include/authenticate.php");
                }
        }
?>
<html>
<head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>捷宏資訊有限公司-<?php echo $jhtitle; ?></title>
        <link rel="stylesheet" href="<?php echo $web; ?>/include/button.css" />
        <link rel="stylesheet" href="<?php echo $web; ?>/include/style1.css" />
        <link rel="stylesheet" href="<?php echo $web; ?>/include/account_table.css" />
        <meta http-equiv="Content-Script-Type" content="text/javascript">
</head>
<body>
<div class="nav">
