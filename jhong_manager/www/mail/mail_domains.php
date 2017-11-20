<?php	
	$buttom		= "mail_server";
	$check_admin 	= "admin";
	$jhtitle     	= "Mail Server 2 domains";
	include ("../include/header.php");
	include ("./mail_function.php");

	if ( isset ( $_REQUEST['action'] ) ) {

		if ( $_REQUEST['action'] == "yes" ) {
			$vhost = $_REQUEST['vhost'];
			$vlist = $_REQUEST['vlist'];
			$mesg = "";
			foreach ( $vhost as $list ) {
				$list = trim(str_replace("\r\n","",$list));
				$list = str_replace(" ","",$list);
				if ( $list != "" ) {
					if ( $mesg == "" ) {
						$mesg = "$list this-text-is-ignored" ;
					} else {
						$mesg = "${mesg}\n$list this-text-is-ignored";
					}
				}
			}
			if ( $mesg == "" ) {
				$log = exec ( "sudo bash -c \"echo '' > /etc/postfix/virtual; postmap /etc/postfix/virtual \" " );
				echo "<script>alert('已刪除所有 domain ！'); location.href='$web/mail/mail_domains.php';</script>";
				die;
			}

			foreach ( $vlist as $list ) {
				$list = trim(str_replace("\r\n","",$list));
				if ( $list != "" ) {
					$mesg = "${mesg}\n$list";
				}
			}

			$log = exec ( "sudo bash -c \"echo '$mesg' > /etc/postfix/virtual; postmap /etc/postfix/virtual \" " );
		}
	}

	$virt_file = fopen ( "/etc/postfix/virtual", "r" ) or die ("Can't open \"/etc/postfix/virtual\" file");
	while ( ! feof($virt_file) ) {
		$temp_info = fgets($virt_file);
		$temp_info = trim(str_replace("\r\n","",$temp_info));
		if ( ! preg_match("/^#/",$temp_info ) && $temp_info != "" ) {
			$temp_row = explode (" ", $temp_info);
			if ( $temp_row[1] == "this-text-is-ignored" ) {
				$virt_host[] = $temp_row[0];
			} else {
				$virt_list[] = $temp_info;
			}
		}
	}

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">兩個郵件網域的管理</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel-group">
                <form method="post" action="mail_domains.php" OnSubmit="return checkme(this)">
                    <div class="panel panel-default">
                        <div class="panel-heading">兩個郵件網域的管理方式</div>
                        <div class="panel-body">
                            <p>假設你在原有的郵件伺服器主機名稱中，又新增了另一個新的郵件主機領域。假設原本的領域名稱為 old.abc.com 好了，新的領域為 new.abc.com 好了。你想要讓這兩個領域的 email 收受者是在不同的實體用戶中！假設 tom@old.abc.com 依舊寄到 tom 這個用戶，但是 tom@new.abc.com 卻是寄到 tom2 這個帳號中！那麼你就得要使用底下的設定方式了！</p>
                            <ul>
                                <li>必須要有實際的 tom2 這個帳號存在；</li>
                                <li>在底下的設定中，你必須要填寫 domain name 為『 new.abc.com 』</li>
                                <li>在後續的設定中，你必須要填寫：『 tom@new.abc.com   tom 2』</li>
                                <li>未來沒有填寫到這個項目中的其他用戶，例如使用 myname@new.abc.com 的郵件來寄送到本機，但是卻沒有將 myname 的紀錄寫入這份資料中，那麼這個 email 將不會被系統收下來。</li>
                                <li>特別注意，new.abc.com 不可以寫入伺服器組態的主機名稱中！</li>
                            </ul>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">第二個郵件領域名稱</div>
                        <div class="panel-body">
                            <p>目前已有的領域名稱： (若全部取消勾選，則後續所有已指定的 email 對應帳號亦將全部刪除喔！)</p>
                            <?php
                                foreach ( $virt_host as $list ) {
                                    echo "<div class='checkbox'><label><input type='checkbox' checked='checked' name='vhost[]' value='$list' />$list</label></div>";
                                }
                            ?>
                            <hr/>
                            <p>底下請填寫第二個郵件領域名稱，一次填寫一個，按下開始修改後，可以再加或勾選上面既有的來刪減</p>
                            <div class="col-lg-4">
                                <input type='text' class="form-control" name='vhost[]' placeholder="ex> new.jhong.com" />
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                    <div class="panel panel-default">
                        <div class="panel-heading">Email 位址與帳號對應設定</div>
                        <div class="panel-body">
                            <p>目前已有的對應設定如下：</p>
                            <?php
                                foreach ( $virt_list as $list ) {
                                    echo "<div class='checkbox'><label><input type='checkbox' checked='checked' name='vlist[]' value='$list' />$list</label></div>";
                                }
                            ?>
                            <hr/>
                            <p>底下請一次新增一個對應值囉：</p>
                            <div class="col-lg-4">
                                <input type='text' class="form-control" name='vlist[]' placeholder="ex> user1@new.jhong.com address1" />
                            </div>
                            <input type="hidden" name="action" value="yes" />
                            <div class="clearfix"></div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">開始修改</button>
                            </div>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </form>
            </div>
            <!-- /.panel-group  -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
	function checkme(f) {
		return confirm("是否確定上傳你的資料？..");
	}
</script>

<?php   include("../include/footer.php"); ?>
