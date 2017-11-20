#!/binbash

systemctl stop    firewalld
systemctl disable firewalld
systemctl restart iptables
systemctl enable  iptables

iptables -F
iptables -X
iptables -Z

iptables -P INPUT DROP
iptables -P OUTPUT ACCEPT
iptables -P FORWARD ACCEPT

iptables -A INPUT -m state --state RELATED,ESTABLISHED -j ACCEPT
iptables -A INPUT -p icmp -j ACCEPT
iptables -A INPUT -i lo -j ACCEPT
iptables -A INPUT -s 59.125.213.145     -j ACCEPT
iptables -A INPUT -s 59.125.213.146     -j ACCEPT
iptables -A INPUT -s 59.125.213.147     -j ACCEPT
iptables -A INPUT -s 122.117.222.8      -j ACCEPT
iptables -A INPUT -s 120.114.142.128/25 -j ACCEPT
iptables -A INPUT -s 120.114.140.0/25 -j ACCEPT

# 1. 確認信任用戶來源
testing=$( cat /jhong/bin/iptables.cracker | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myip in $( cat /jhong/bin/iptables.cracker )
	do
		iptables -A INPUT -s ${myip} -j DROP
	done
fi

# 1. 確認信任用戶來源
testing=$( cat /jhong/bin/iptables.trustip | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myip in $( cat /jhong/bin/iptables.trustip )
	do
		iptables -A INPUT -s ${myip} -j ACCEPT
	done
fi

# 2. 確認信任服務
testing=$( cat /jhong/bin/iptables.tcpservice | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myport in $( cat /jhong/bin/iptables.tcpservice )
	do
		iptables -A INPUT -p tcp --dport ${myport} --sport 1024:65534 -j ACCEPT
	done
fi

testing=$( cat /jhong/bin/iptables.udpservice | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myport in $( cat /jhong/bin/iptables.udpservice )
	do
		iptables -A INPUT -p udp --dport ${myport} --sport 1024:65534 -j ACCEPT
	done
fi

# 3. 確認信任埠口
testing=$( cat /jhong/bin/iptables.tcpport | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myport in $( cat /jhong/bin/iptables.tcpport )
	do
		iptables -A INPUT -p tcp --dport ${myport} --sport 1024:65534 -j ACCEPT
	done
fi

testing=$( cat /jhong/bin/iptables.udpport | sed 's/ //g')
if [ "$testing" != "" ]; then
	for myport in $( cat /jhong/bin/iptables.udpport )
	do
		iptables -A INPUT -p udp --dport ${myport} --sport 1024:65534 -j ACCEPT
	done
fi

# 3. 確認信任埠口
iptables -A INPUT -j REJECT 

iptables-save > /etc/sysconfig/iptables

