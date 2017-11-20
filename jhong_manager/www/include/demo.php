<!doctype html>
<html>
<head>
	<meta charset="utf8" />
	<title>大專生微產學計劃案界面開發專案</title>
	<link rel="stylesheet" href="/include/style.css" />
</head>
<body>

<div class="page">

<header>
	<p style="font-size:18pt; color: darkblue; margin-top: 15px; font-weight: bold;text-align: center; ">歡迎光臨微產學界面開發案</p>
</header>

<nav>
	<?php include("/var/www/jhong/include/menu.php"); ?>
</nav>

<div class="content">
	<h1>網路芳鄰的磁碟機設定</h1>
	<form >
		資源分享名：<input type="text" name="smbname" style="width: 250px;" /><br />
		資源註記名：<input type="text" name="smbcomm" style="width: 250px;" /><br />
		Linux目錄：<input type="text" name="smbpath" style="width: 250px;" /><br />
		可寫入與否：<input type="text" name="smbwrite" style="width: 250px;" /><br />
		可寫入帳號：<input type="text" name="smbuser" style="width: 250px;" /><br />

	</form>
</div>

<footer>
	網站設計： 崑山資傳系@dic.ksu since 2013~2013
</footer>


</div> <!-- page 的結尾 -->
</body>
</html>
