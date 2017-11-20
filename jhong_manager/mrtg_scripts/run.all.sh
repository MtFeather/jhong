#!/bin/bash
# 全部照順序跑!!

# 先確認 mrtg 的語系改成 UTF8 了！
checking=$( grep iso-8859-1 /bin/mrtg )
if [ "$checking" != "" ]; then
	echo "change charset from iso to utf8"
	sed -i 's/iso-8859-1/utf8/g' /bin/mrtg
fi

export LC_ALL=C
LANG=C LC_ALL=C /usr/bin/mrtg /jhong/jhong_manager/mrtg_scripts/mrtg.cfg.cpu  --lock-file /var/lock/mrtg/mrtg_l --confcache-file /var/lib/mrtg/mrtg.ok
LANG=C LC_ALL=C /usr/bin/mrtg /jhong/jhong_manager/mrtg_scripts/mrtg.cfg.mem  --lock-file /var/lock/mrtg/mrtg_l --confcache-file /var/lib/mrtg/mrtg.ok
LANG=C LC_ALL=C /usr/bin/mrtg /jhong/jhong_manager/mrtg_scripts/mrtg.cfg.net  --lock-file /var/lock/mrtg/mrtg_l --confcache-file /var/lib/mrtg/mrtg.ok
LANG=C LC_ALL=C /usr/bin/mrtg /jhong/jhong_manager/mrtg_scripts/mrtg.cfg.disk --lock-file /var/lock/mrtg/mrtg_l --confcache-file /var/lib/mrtg/mrtg.ok
