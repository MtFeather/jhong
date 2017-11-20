#!/bin/bash

PATH=/bin:/sbin:/usr/bin:/usr/sbin
source="../source/source.sh"
filename=/dev/shm/nmcli_file_check

eths=$( nmcli connection show | cut -d ' ' -f1 | grep -v NAME )

for eth in $eths
do
	nmcli connection show "$eth" > $filename
	onboot=$(cat $filename | grep 'connection.autoconnect:' | cut -d ':' -f2 | sed 's/ //g')
	method=$(cat $filename | grep 'ipv4.method:' | cut -d ':' -f2 | sed 's/ //g')
	if [ "$method" == "auto" ]; then
		ipaddr=$(cat $filename | grep 'IP4.ADDRESS' | cut -d ':' -f2 | sed 's/ //g' | sed 's/,.*$//g')
		gwaddr=$(cat $filename | grep 'IP4.GATEWAY' | cut -d ':' -f2 | sed 's/ //g')
		dns=$(cat $filename | grep 'IP4.DNS' | cut -d ':' -f2 | sed 's/ //g')
		ro="readonly='readonly'"
	else
		ipaddr=$(cat $filename | grep 'ipv4.addresses'|cut -d ':' -f2|sed 's/ //g' | sed 's/,.*$//g')
		gwaddr=$(cat $filename | grep 'ipv4.gateway'|cut -d ':' -f2|sed 's/ //g'|sed 's/}//g')
		dns=$(cat $filename | grep 'ipv4.dns' | cut -d ':' -f2 | sed 's/ //g')
		ro=''
	fi

	echo "<form class='form-horizontal' method='post' name='${eth}' OnSubmit='return netcheck()'>
                  <div class='panel panel-default'>
                      <div class='panel-body'>"

	# Device name
	echo "<div class='form-group'>
                  <label class='control-label col-lg-2'>裝置界面名稱:</label>
                  <div class='col-lg-4'>
                      <p class="form-control-static">${eth}</p>
                  </div>
	          <input type='hidden' name='device' value='${eth}' />
              </div>"

	# Onboot is yes or no
	if [ "$onboot" == "yes" ]; then
		onbooty="selected='selected'"; onbootn=""
	else
		onbootn="selected='selected'"; onbooty=""
	fi
	echo "<div class='form-group'>
                  <label class='control-label col-lg-2'>自動啟動:</label>
                  <div class='col-lg-1'>
                      <select class='form-control' name='onboot'>
                          <option value='yes' $onbooty >Yes</option>
                          <option value='no'  $onbootn >No</option>
	              </select>
                  </div>
              </div>"

	# do you need dhcp(auto) or none(manual)
	if [ "$method" == "auto" ]; then
		methody="selected='selected'"; methodn=""
	else
		methodn="selected='selected'"; methody=""
	fi
	echo "<div class='form-group'>
                  <label class='control-label col-lg-2'>網路參數模式:</label>
                  <div class='col-lg-4'>
                      <select class='form-control' name='method'>
                          <option value='auto'   $methody >自動取得(DHCP)</option>
                          <option value='manual' $methodn >手動設定</option>
                      </select>
                  </div>
              </div>"

	# IP address / Netmask
	echo "<div class='form-group'>
                <label class='control-label col-lg-2'>IP Address/Netmask:</label>
                <div class='col-lg-4'>
                    <input type='text' class='form-control' name='ipaddr' value='${ipaddr}'/>
                </div>
                <div class='col-lg-4'>
                      <p class="form-control-static">(Ex&gt; 192.168.0.1/24)</p>
                </div>
              </div>"

	# Gateway address
	echo "<div class='form-group'>
                  <label class='control-label col-lg-2'>Gateway Address:</label>
                  <div class='col-lg-4'>
                      <input type='text' class='form-control' name='gwaddr' value='${gwaddr}'/>
                  </div>
              </div>"

	# DNS Address
	echo "<div class='form-group'>
                  <label class='control-label col-lg-2'>DNS Address:</label>
                  <div class='col-lg-4'>
                      <input type='text' class='form-control' name='dns' value='${dns}' />
                  </div>
              </div>"

	# Submit
	echo "<div class='form-group'>
                  <div class='col-lg-offset-5 col-lg-2'>
                      <button type=submit class='btn btn-primary' name='subeth'>開始修改</button>
                  </div>
              </div>"

	echo "        </div>
                      <!-- /.panel-body -->
                  </div>
                  <!-- /.panel -->
              </form>"
done

exit



eths=$( /sbin/ifconfig -a | grep '^eth' | cut -d ' ' -f 1 )
for eth in $eths
do
	
echo "<tr><td><b>DEVICE</b></td><td><b>：</b>$eths
			</td><td><span class='smallfont'><b>第幾張網卡</b></span></td></td></tr>
      <tr><td><b>ONBOOT</b></td><td><b>：</b>$onboot</td></tr>
      <tr><td><b>BOOTPROTO</b></td><td><b>：</b>$bootproto</td></tr>
      <tr><td><b>IPADDR</b></td><td><b>：</b>$ipaddr</td></tr>
      <tr><td><b>NETMASK</b></td><td><b>：</b>$netmask</td></tr>
      <tr><td><b>GATEWAY</b></td><td><b>：</b>$gateway</td></tr>\
      <tr><td><b>DNS</b></td><td><b>：</b>$dns</td>
      <td>
	<a href='sys_mod_ip.php?name=$eths'><input type='submit' name='smb' class='userbutton' value='修改'></a>
      </td></tr>
	"
done
