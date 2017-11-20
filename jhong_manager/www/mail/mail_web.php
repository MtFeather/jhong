<?php 
	$buttom      = "mail_server";
	$check_admin = "admin";
	$jhtitle     = "Mail server 目前的狀況";
	include ( "../include/header.php" );

	if ( isset ( $_REQUEST['action'] ) ) {
		if ( $_REQUEST['action'] == "upload" ) {
			// 設定要上傳的位置與檔名
			$target_dir = "/usr/share/roundcubemail/skins/larry/images/";
			$check_file  = $target_dir . basename($_FILES["filetoupload"]["name"]);
			$target_file = $target_dir . "roundcube_logo.png";
			$uploadOk = 1;
			$imageFileType = pathinfo($check_file,PATHINFO_EXTENSION);

			// Check if image file is a actual image or fake image
			$check = getimagesize($_FILES["filetoupload"]["tmp_name"]);
			if($check !== false) {
				echo "檔案是 - " . $check["mime"] . ".";
				$uploadOk = 1;
			} else {
				echo "檔案並非圖形檔";
				$uploadOk = 0;
			}

			if ($_FILES["filetoupload"]["size"] > 500000) {
				echo "抱歉，檔案容量大於 500K 了！不支援！";
				$uploadOk = 0;
			}

			if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
				echo "抱歉！目前圖檔的副檔名只支援 .jpg, .png, .jpeg, .gif 等等喔！";
				$uploadOk = 0;
			}

			if ($uploadOk == 0) {
				echo "上傳失敗！";
				echo "<script>alert('上傳失敗！請與管理員聯絡！'); location.href='$web/mail/mail_web.php';</script>";
			} else {
				if (move_uploaded_file($_FILES["filetoupload"]["tmp_name"], $target_file)) {
					echo "The file ". basename( $_FILES["filetoupload"]["name"]). " has been uploaded.";
					echo "<script>alert('上傳成功囉！請自行檢查網頁是否順利展示新 Logo'); location.href='$web/mail/mail_web.php';</script>";
				} else {
					echo "Sorry, there was an error uploading your file.";
					echo "<script>alert('上傳失敗！請與管理員聯絡！'); location.href='$web/mail/mail_web.php';</script>";
				}
			}
		}
	}


	$log = shell_exec ( "sudo echo \$(ifconfig | grep 'inet '| grep -v 127.0.0.1 | awk '{print \$2}') " );
	$mailip = explode ( " ", trim(str_replace("\r\n","",$log)) );

?>
<style>
.btn-file {
    position: relative;
    overflow: hidden;
}
.btn-file input[type=file] {
    position: absolute;
    top: 0;
    right: 0;
    min-width: 100%;
    min-height: 100%;
    font-size: 100px;
    text-align: right;
    filter: alpha(opacity=0);
    opacity: 0;
    outline: none;
    background: white;
    cursor: inherit;
    display: block;
}
</style>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">WebMail 與收發信件注意事項</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class='row'>
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">Webmail</div>
                <div class="panel-body">
                    <p>你的 WebMail 連結，應該可以從底下的連結去挑選的喔！要注意的是，本系統考量到目前資料應該得要加密才行，因此已經將 webmail 放至於 https:// 的協定下！請安心使用！</p>
                    <ul>
                    <?php	
                        foreach ( $mailip as $temp ) {
                            echo "<li><a href='https://$temp/webmail' target='_blank'>https://$temp/webmail</a></li>";
                        }
                    ?>
                    </ul>
                    <hr/>
                    <p>目前的網頁 logo 如下所示：</p>
                    <img src="https://<?php echo $mailip[0]; ?>/webmail/skins/larry/images/roundcube_logo.png" class="img-thumbnail"/>
                    <p>你可以點選底下的按鈕來選擇上傳修改該 Logo 顯示：</p>
                    <form action="mail_web.php" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>選擇檔案：</label>
                            <div class="col-lg-4  input-group">
                                <input type="text" class="form-control" value="未選擇檔。" readonly>
                                <label class="input-group-btn">
                                    <span class="btn btn-default">
                                        瀏覽&hellip; <input type="file" name="filetoupload" id="filetoupload" style="display: none;" multiple />
                                    </span>
                                </label>
                            </div>
                        </div>
                        <input type="hidden" name="action" value="upload" />
                        <button type="submit" name="submit" class="btn btn-primary" >上傳圖形檔</button>
                    </form>
                    <hr />
                    <p>至於收信的部份，本系統除了提供上述的 webmail 方式外，亦提供加密的 POP3s 以及 IMAPs 兩種方式來提供您遠端連線至本系統喔！若有任何設定方面的問題，請詢問捷宏資訊公司。</p>
                    <hr />
                    <p>在使用類似 outlook 等收信軟體時，由於使用 IMAPs/POP3s 等加密機制，因此 outlook 將會要求您信任憑證。您可以透過 outlook 的憑證系統來安裝憑證，或是<a href="jhong.zip">直接下載本系統憑證</a>資料，下載該檔案後：</p>
                    <ol>
                        <li>將該檔案解壓縮到 C:\jhong 目錄下；</li>
                        <li>變更目錄到 c:\jhong </li>
                        <li>內部會有 install.bat 安裝檔，請將滑鼠移動到該檔案上，然後『以系統管理員權限』來執行該程式，即可順利載入憑證，未來將不需要再次安裝該憑證。</li>
                        <li>你的 outlook 要使用這部伺服器的帳號中，他的『內送郵件伺服器』及『外寄郵件伺服器』的名稱不可寫IP，一定要寫『你的FQDN』才行</li>
                        <li>承上，例如 jhong.com.tw 為你的伺服器，那麼外寄、內送伺服器均須設定為 jhong.com.tw 才行！</li>
                    </ol>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
</div>
<!-- /.container-fluid -->
<script>
$(function() {

  // We can attach the `fileselect` event to all file inputs on the page
  $(document).on('change', ':file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
  });

  // We can watch for our custom `fileselect` event like this
  $(document).ready( function() {
      $(':file').on('fileselect', function(event, numFiles, label) {

          var input = $(this).parents('.input-group').find(':text'),
              log = numFiles > 1 ? numFiles + ' files selected' : label;

          if( input.length ) {
              input.val(log);
          } else {
              if( log ) alert(log);
          }

      });
  });
});
</script>
<?php   include( "../include/footer.php" ); ?>
