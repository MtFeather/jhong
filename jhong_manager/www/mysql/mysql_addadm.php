<?php	
	$buttom		= "sql";
	$check_admin 	= "admin";
	$jhtitle     	= "SQL 資料庫管理";
	include("../include/header.php");
	include("./mysql_config.php");
	include("./mysql_function.php");

	// 先要確認有這個資料庫的輸入才行！
	if ( ! isset ( $_REQUEST['database'] ) ) {
		echo "<script>alert('沒有填寫資料庫名稱，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
		die;
	}

	$databasename = $_REQUEST['database'];

	// 先確認這個資料庫不可以是重要的資料庫檔名！先排除防呆一下！
	$checkdata = checkdatabasename($databasename);
	if ( $checkdata != "OK" ) {
		echo "<script>alert('系統無此資料庫名稱，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
		die;
	}

	$existdata = 0;
	$datasql = "show databases";
	$datares = mysql_query($datasql,$link2);
	while ( $row = mysql_fetch_row ($datares) )  {
		if ( $row[0] == "$databasename" ) {
			$existdata = 1;
		}
	}

	if ( $existdata == 0 ) {
		echo "<script>alert('系統無此資料庫名稱，請檢查列表資訊'); location.href='$web/mysql/mysql_show.php';</script>";
		die;
	}

	if ( isset ( $_REQUEST['action'] ) ) {

		$database = $_REQUEST['database'];
		$username = $_REQUEST['username'];

		// 新增管理原部分
		if ( $_REQUEST['action'] == "add" ) {

			$sqlres = "grant all privileges on $database.* to '$username'@'localhost' ";
			$logres = mysql_query($sqlres,$link2);

			$tempres = mysql_query ( "flush privileges", $link2);
                       	echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_addadm.php?database=$database';</script>";
                       	die;
		}

		// 刪除管理員部分
		if ( $_REQUEST['action'] == "del" ) {

                        $sqlres = "delete from db where User = '$username' and $sysuser and Db = '$database'";
                        $logres = mysql_query($sqlres,$link2);

			$tempres = mysql_query ( "flush privileges", $link2);
                       	echo "<script>alert('修改完畢，請檢查列表資訊'); location.href='$web/mysql/mysql_addadm.php?database=$database';</script>";
                       	die;
		}

		// 上傳匯入資料喔
		if ( $_REQUEST['action'] == "upload" ) {

			$dataimport = $_REQUEST['dataimport'];
			$importfile = "/dev/shm/jhong/dataimportfile.txt";
			$log = exec ( "sudo bash -c \"[ ! -d /dev/shm/jhong ] && mkdir /dev/shm/jhong; rm $importfile; touch $importfile; chmod 666 $importfile \" " );

			$importlog  = fopen ( $importfile, "w" ) or die ("Can't open \"$importfile\" file");
			fwrite($importlog, $dataimport);
			fclose($importlog);

			$log = shell_exec ( "mysql -u root -p${sql_passwd2} $database < $importfile " );

			$tempres = mysql_query ( "flush privileges", $link2);
                       	echo "<script>alert('匯入完畢，請檢查資訊'); location.href='$web/mysql/mysql_addadm.php?database=$database';</script>";
                       	die;
		}
	}

	// 計算 tables 數量
	mysql_select_db ( $databasename, $link2 );			// 轉換資料庫，為了計算容量
	$tables = 0;
	$tables_size = 0;
	$tres = mysql_query("show table status", $link2);
	while ( $trow = mysql_fetch_row($tres) ) {
		$tables = $tables + 1;
		$tables_size = $tables_size + $trow[6] + $trow[8];
	}

	$tables_size = $tables_size / 1024;
	if ( $tables_size < 1024 ) {
		$tables_unit = " Kbyes";
	} else {
		$tables_size = $tables_size / 1024;
		$tables_unit = " Mbyes";
	}

	mysql_select_db ( $sql_data2, $link2 );				// 換回我們的資料庫
?>
<style>
    .table > thead > tr > th, .table > tbody > tr > td {
        vertical-align: middle;
        text-align: center;
    }
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">SQL 資料庫管理/匯入/匯出</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <caption style="border: inherit; background-color: #F5F5F5; text-align: center;">新增/刪除 '<?php echo $databasename; ?>' 資料庫管理員作業</caption>
                    <thead>
                        <tr>
                            <th>既有管理員名單(點選可刪除)</th>
                            <th>新增管理員名單</th>
                            <th>表格數</th>
                            <th>資料總量</th>
                            <th>資料匯出下載</th>
                        </tr>
                    </thead>
                    <tbody>            
                        <tr>
                            <td>
                            <?php 	
                                $tempsql = "select User from db where Db = '$databasename' order by User"; 
                                $tempres = mysql_query($tempsql, $link2 );
                                $i = 0;
                                while ( $temprow = mysql_fetch_row($tempres) ) {
                                    $existuser[$i] = $temprow[0];
                                    $i++;
                                    echo "<a href='mysql_addadm.php?action=del&database=$databasename&username=$temprow[0]' class='btn btn-info' Onclick='return checkdel()'>$temprow[0]</a>  ";
		                }
                            ?>
                            </td>
                            <td>
                                <form class="form-inline" method="post" OnSubmit="return checkadd(this)">
                                    <select name="username" class="form-control">
                                        <option value="no">請選擇</option>
                                        <?php
                                            $usersql = "select User from user where $sysuser and Host = 'localhost' order by User";
                                            $userresult = mysql_query($usersql,$link2);
                                            while ( $userrow = mysql_fetch_row($userresult ) )  {
                                                $showit = "yes";
                                                foreach ( $existuser as $tempuser ) {
                                                    if ( $tempuser == $userrow[0] ) $showit = "no";
                                                }
                                                if ( $showit == "yes" ) echo "<option value='$userrow[0]'>$userrow[0]</option>";
                                            }
                                        ?>
                                    </select>
                                    <input type="hidden" name="database" value="<?php echo $databasename; ?>" />
                                    <input type="hidden" name="action" value="add" />
                                    <button type="submit" class="btn btn-primary">新增管理員</button>
                                </form>
                            </td>
                            <td><?php echo $tables;  ?></td>
                            <td><?php printf("%1.2f",$tables_size);  echo $tables_unit; ?></td>
                            <td><a href="mysql_file.php?database=<?php echo $databasename; ?>&action=backup" class='btn btn-warning' Onclick='return checkbackup()'>備份</a></td>
                        </tr>
	            </tbody>
                </table>
            </div>
            <!-- /.table-responsive -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <caption style="border: inherit; background-color: #F5F5F5; text-align: center;">進行 '<?php echo $databasename; ?>' 資料庫匯入作業</caption>
                    <tbody>
                        <tr>
                            <td>
                                <form action="mysql_addadm.php" method="post" OnSubmit="return checkupload(this)" >
                                    <textarea class="form-control" name="dataimport" rows="15" placeholder="下載的資料打開後，全部複製後，貼上到這裡！"></textarea>
                                    <input type="hidden" name="action" value="upload" />
                                    <input type="hidden" name="database" value="<?php echo $databasename; ?>" />
                                    <br/>
                                    <button type="submit" class="btn btn-primary">開始匯入</button>
                                </form>         	
                            </td>
                        </tr>
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
<script>
	function checkadd(f) {
		if ( f.username.value == "no" ) {
			alert("未選擇管理員帳號");
			return false;
		}
		return confirm("是否確定要增加管理員？");
	}
	function checkdel(f) {
		return confirm("是否確定要刪除管理員？");
	}

        function checkbackup() {
                return confirm("是否確定要備份且下載此資料庫？");
        }

        function checkupload(f) {
		if ( f.dataimport.value == "" ) {
			alert("妳沒有填入上傳的資料喔");
			return false;
		}
                return confirm("是否確定要匯入資料庫內容了？");
        }
</script>

<?php   include("../include/footer.php"); ?>
