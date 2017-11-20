<?php 
	$buttom      = "system";
	$check_admin = "user";
	$jhtitle     = "磁碟陣列的相關狀態";
	include ( "../include/header.php" );

	$thismsg1 = shell_exec ( "sudo sh /root/bin/myraid.sh controller " );
	$thismsg2 = shell_exec ( "sudo sh /root/bin/myraid.sh device " );
	$thismsg3 = shell_exec ( "sudo sh /root/bin/myraid.sh array " );
	$thismsg4 = shell_exec ( "sudo bash -c \" raid=\\\$(sh /root/bin/myraid.sh array | cut -d ' ' -f 1| grep '^[0-9]'); if [ \\\"\\\${raid}\\\" != \\\"\\\" ]; then for i in \\\${raid}; do sh /root/bin/myraid.sh array \\\${i}; echo \\\"<hr />\\\"; done;   fi \" " );

?>
        <h1>磁碟陣列現階段的系統狀況顯示</h1>

	<table style="width: 97%; text-align:left; border: 2px solid gray;" class="account_table">
	<tr><th>磁碟陣列卡相關資訊</th></tr>
	<tr><td style='text-align:left;'><pre style="text-align:left;margin:0; font-size: 9pt; color: black;">
<?php	echo $thismsg1;	?>
	</pre></td></tr>
	<tr><th>磁碟陣列內各個硬碟相關資訊</th></tr>
	<tr><td style='text-align:left;'><pre style="text-align:left;margin:0; font-size: 9pt; color: black;">
<?php	echo $thismsg2;	?>
	</pre></td></tr>
	<tr><th>已建立之磁碟陣列相關資訊</th></tr>
	<tr><td style='text-align:left;'><pre style="text-align:left;margin:0; font-size: 9pt; color: black;">
<?php	echo $thismsg3;	?>
	</pre></td></tr>
	<tr><th>各磁碟陣列的詳細內部設定</th></tr>
	<tr><td style='text-align:left;'><pre style="text-align:left;margin:0; font-size: 9pt; color: black;">
<?php	echo $thismsg4;	?>
	</pre></td></tr>
	</table>

<?php   include( "../include/footer.php" ); ?>
