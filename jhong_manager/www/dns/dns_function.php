<?php
/* ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$dnsnumber				// DNS 的 zone 總數
	$dns_zonename[$dnsnumber]		// DNS 的 zone 名稱，就是 domain name 囉
	$dns_zonefile[$dnsnumber]		// DNS 的 zone 設定所在的檔名喔
	$dns_ttl[$dnsnumber]			// TTL 
	$dns_soa[$dnsnumber]			// SOA
	$dns_serial[$dnsnumber]			// serial
	$dns_ns[$dnsnumber]			// NS 主機名稱
	$dns_nsip[$dnsnumber]			// NS 的主機 IP 
	$dns_mx[$dnsnumber]			// MX 主機名稱
	$dns_mxip[$dnsnumber]			// MX 主機的 IP
	$dns_hostname[$dnsnumber][$hostnu]	// A 的主機名稱
	$dns_hostip[$dnsnumber][$hostnu]	// A 的主機 IP 喔！

*/ ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//include ("../include/header.php");

	/*echo date("Y/m/d H:i:s"). substr((string)microtime(), 1, 6);
	echo "<br />";*/
	// 先檢查看看基本設定有沒有問題
	$log = shell_exec ( "sudo sh $sdir/dns/dns_check_config.sh 2>&1" );

	if ( ! isset ( $www ) ) {
		echo "沒有權限閱讀此頁面喔!";
		echo "<meta http-equiv=REFRESH CONTENT=3;url=../index.php>";
		die;
	}

	// 找出可以支援的信任網域：
	$trustnetwork_temp0 = shell_exec ( "sudo grep trustnetwork /etc/named.conf | head -n 1 " );
	$oldtrustnetwork = $trustnetwork_temp0;
	$trustnetwork_temp1 = explode ( "{", $trustnetwork_temp0 );
	$trustnetwork_temp2 = explode ( "}", $trustnetwork_temp1[1] );
	$trustnetwork_temp3 = $trustnetwork_temp2[0];
	$trustnetwork_temp4 = str_replace ( ";", "", $trustnetwork_temp3 );
	$trustnetwork = explode ( " ", trim($trustnetwork_temp4) );
	//foreach ( $trustnetwork as $temp ) echo "'$temp',";
	// 先取得原始的系統設定內容
	$dnsfile_zone = fopen ( "/etc/named/named.jhong.conf", "r" ) or die ("Can't open \"/etc/named/named.jhong.conf\" file");
	$i = 0;
	$dnsnumber = 0;
	while ( ! feof($dnsfile_zone) ) {
		$temp_info = fgets($dnsfile_zone);
		if ( trim($temp_info) != "" ) {
			$temp_line = explode ( '"', $temp_info );
			$dnsnumber = $dnsnumber + 1;
			$dns_zonename[$dnsnumber] = trim( $temp_line[1] );
			$dns_zonefile[$dnsnumber] = "/var/named/" . trim( $temp_line[3] );
			$temp_file = fopen( $dns_zonefile[$dnsnumber], "r" ) or die ("Can't open \"$dns_zonefile[$dnsnumber]\" file");
			$hostnu = 0;
			while ( ! feof ( $temp_file ) ) {
				$file_line1 = fgets ( $temp_file );
				$file_line2 = preg_split ("/[\t]/", $file_line1 );
				if ( trim($file_line2[0]) == '$TTL' )   	   {$dns_ttl[$dnsnumber]  = trim( $file_line2[1] );continue;}
				if ( trim($file_line2[1]) == 'IN SOA' )	   {
					$dns_soa[$dnsnumber]  = trim( $file_line2[2] );
					$thistemp = explode(" ",$dns_soa[$dnsnumber] );
					$dns_serial[$dnsnumber] = trim( $thistemp[3] );
					continue;
				}
				if ( trim($file_line2[1]) == 'IN NS' )  	   {$dns_ns[$dnsnumber]   = trim( $file_line2[2] );continue;}
				if ( trim($file_line2[0]) == $dns_ns[$dnsnumber] ) {$dns_nsip[$dnsnumber] = trim( $file_line2[2] );continue;}
				if ( trim($file_line2[1]) == 'IN MX 10' )  	   {$dns_mx[$dnsnumber]   = trim( $file_line2[2] );continue;}
				if ( trim($file_line2[0]) == $dns_mx[$dnsnumber] ) {$dns_mxip[$dnsnumber] = trim( $file_line2[2] );continue;}
				if ( trim($file_line2[1]) == 'IN A' ) {
					if ( trim($file_line2[0]) != $dns_ns[$dnsnumber] && trim($file_line2[0]) != $dns_mx[$dnsnumber] ) {
						$hostnu = $hostnu + 1;
						$dns_hostname[$dnsnumber][$hostnu] = trim($file_line2[0]);
						$dns_hostip[$dnsnumber][$hostnu]   = trim($file_line2[2]);
					}
				}
			}
			$dns_mx[$dnsnumber] = preg_replace('/\.$/','',$dns_mx[$dnsnumber]);
			$i++;
			if ( $i > 500 ) die;
		}
	}

	/*echo "$dnsnumber <br />";
	for ( $i=1; $i<=$dnsnumber; $i++ ) {
		echo "$dns_zonename[$i]|$dns_zonefile[$i]|$dns_ttl[$i]|$dns_soa[$i]|<br />";
		echo "$dns_ns[$i]|$dns_nsip[$i]|$dns_mx[$i]|$dns_mxip[$i]<br /> hostname=";
		foreach ( $dns_hostname[$i] as $temp ) echo "${temp},";
		echo "hostip=";
		foreach ( $dns_hostip[$i] as $temp ) echo "${temp},";
	}*/

?>

