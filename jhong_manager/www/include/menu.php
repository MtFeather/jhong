<?php
	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}
?>
<div class="navbar-default sidebar" role="navigation">
    <div class="sidebar-nav navbar-collapse">
        <ul class="nav" id="side-menu">
<?php	// User is NOT login this system.
	if ( ! isset ($_SESSION['username']) ) { ?>
            <div class="sidebar-search">
                <p style="color:red; font-weight: bolder;">請先登入系統
                <?php if ( isset ( $login_msg )) echo '
                <div class="alert alert-danger alert-dismissable alert-sm">
                    <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                    <strong>'. $login_msg .'</strong> 
                </div>' ; ?>
                <form name="form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                    <div class="form-group">
                        <label>帳號：</label>
                        <input type="text" class="form-control" placeholder="請輸入帳號" name="username" autofocus>
                    </div>
                    <div class="form-group">
                        <label>密碼：</label>
                        <input type="password" class="form-control" placeholder="請輸入密碼" name="pw">
                    </div>
                    <button type="submit" class="btn btn-success pull-right">登入</button>
                    <input type="hidden" name="logincheck" value="yes" />
                </form>
            </div>
<?php } // User is already login this system.
	elseif ($_SESSION['username'] != null && $_SESSION['username'] == $sql_username )  { ?>
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "new" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-bullhorn"></i> 最新消息/留言發佈<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/sql/myprofile.php">我的帳號資訊與管理</a></li>
                    <li><a href="<?php echo $web; ?>/index.php">查閱最新消息</a></li>
                    <li><a href="<?php echo $web; ?>/sql/news.php">發布最新消息</a></li>
                    <?php if ( $_SESSION['userlevel'] == "admin" ) { ?>
                    <li><a href="<?php echo $web; ?>/sql/user_list.php">網頁帳號列表管理</a></li>
                    <li><a href="<?php echo $web; ?>/sql/user_add.php">新增網頁帳號</a></li>
                    <?php } ?>
                </ul>
            </li>
            <!-- /.nav-news -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "system" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-dashboard"></i> 系統狀態查詢與管理<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/system/sys_info.php">系統資訊/修訂時間</a></li>
                    <?php if ( $_SESSION['userlevel'] == "admin" ) { ?>
                    <li><a href="<?php echo $web; ?>/system/sys_crontab.php">系統工作排程</a></li>
                    <li><a href="<?php echo $web; ?>/system/sys_ip.php">網路位址(IP)查看修改</a></li>
                    <li><a href="<?php echo $web; ?>/system/sys_service.php">服務狀態與開關機</a></li>
                    <li><a href="<?php echo $web; ?>/system/sys_firewall.php">防火牆觀察與設定</a></li>
                    <li><a href="<?php echo $web; ?>/system/sys_backup.php">以外接設備來備份</a></li>
                    <?php } ?>
                    <li><a href="<?php echo $web; ?>/system/sys_version.php">系統資訊圖表查看</a></li>
                    <?php if ( trim($check_raid) == "yes" ) { ?>
                    <li><a href="<?php echo $web; ?>/system/sys_raid.php">磁碟陣列狀態觀察</a></li>
                    <?php } ?>

                </ul>
            </li>
            <!-- /.nav-system -->
            <?php if ( $_SESSION['userlevel'] == "admin" ) { ?>
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "account" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-group"></i> 使用者帳號/群組管理<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/account/user_time.php">帳號列表與管理</a></li>
                    <li><a href="<?php echo $web; ?>/account/user_groups.php">群組列表與管理</a></li>
                    <li><a href="<?php echo $web; ?>/account/user_add.php">系統帳號建置</a></li>
                    <li><a href="<?php echo $web; ?>/account/user_bks.php">批次帳號凍/解</a></li>
                </ul>
            </li>
            <!-- /.nav-account -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "samba_server" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-folder"></i> samba伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/samba/samba_show.php">資源列表與管理</a></li>
                    <li><a href="<?php echo $web; ?>/samba/samba_add.php">新增網芳資源分享</a></li>
                    <li><a href="<?php echo $web; ?>/samba/samba_status.php">目前系統與掛載狀況</a></li>
                </ul>
            </li>
            <!-- /.nav-samba -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "dns_server" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-compass"></i> DNS伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/dns/zone_show.php">領域名稱列表與管理</a></li>
                    <li><a href="<?php echo $web; ?>/dns/zone_query.php">查詢領域名稱</a></li>
                    <li><a href="<?php echo $web; ?>/dns/zone_add.php">新增一個領域名稱</a></li>
                    <li><a href="<?php echo $web; ?>/dns/zone_manager.php">修改存在的領域名稱</a></li>
                    <li><a href="<?php echo $web; ?>/dns/zone_status.php">目前 DNS 系統狀態</a></li>
                </ul>
            </li>
            <!-- /.nav-samba -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "ftp_server" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-cubes"></i> FTP伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/ftp/ftp_show.php">使用者狀態觀察/管理</a></li>
                    <li><a href="<?php echo $web; ?>/ftp/ftp_status.php">目前 FTP 系統狀態</a></li>
                </ul>
            </li>
            <!-- /.nav-ftp -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "apache" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-globe"></i> WWW 伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/www/www_show.php">整體伺服器查閱與設定</a></li>
		    <!--
                    <li><a href="<?php echo $web; ?>/www/www_addvirtual.php">新增虛擬主機設定</a></li>
                    <li><a href="<?php echo $web; ?>/www/www_virtuals.php">查看虛擬主機設定值</a></li>
		    -->
                    <li><a href="<?php echo $web; ?>/www/www_status.php">WWW 伺服器狀態</a></li>
                </ul>
            </li>
            <!-- /.nav-apache -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "mail_server" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-envelope"></i> 電子郵件伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/mail/mail_show.php">電子郵件組態設定</a></li>
                    <li><a href="<?php echo $web; ?>/mail/mail_greylist.php">設定黑、白名單</a></li>
                    <li><a href="<?php echo $web; ?>/mail/mail_queue.php">佇列資料觀察/管理</a></li>
                    <li><a href="<?php echo $web; ?>/mail/mail_domains.php">兩個郵件網域管理</a></li>
                    <li><a href="<?php echo $web; ?>/mailscanner">MailWatch 郵件分析</a></li>
                    <li><a href="<?php echo $web; ?>/mail/mail_web.php">WebMail</a></li>
                    <li><a href="<?php echo $web; ?>/mail/mail_status.php">Mail 系統狀態</a></li>
                </ul>
            </li>
            <!-- /.nav-mail -->
            <li>
                <a <?php if( isset ($buttom)) { if ( $buttom == "sql" ) echo "class='active'";} ?>
                href="#"><i class="fa fa-database"></i> SQL 伺服器<span class="fa arrow"></span></a>
                <ul class="nav nav-second-level">
                    <li><a href="<?php echo $web; ?>/mysql/mysql_show.php">資料庫觀察/管理</a></li>
                    <li><a href="<?php echo $web; ?>/mysql/mysql_user.php">用戶觀察/管理</a></li>
                    <li><a href="<?php echo $web; ?>/mysql/mysql_status.php">SQL 系統狀態</a></li>
                </ul>
            </li>
            <!-- /.nav-mail -->
            <?php } ?>
        </ul>
    </div>
    <!-- /.sidebar-collapse -->
</div>
<!-- /.sidebar-collapse -->
<?php } else {
	echo "no";
}
?>

