#!/bin/bash
PATH=/bin:/sbin:/usr/bin:/usr/sbin
source="../source/source.sh"
cpuname=$(cat /proc/cpuinfo | grep 'name' | cut -d ':' -f 2 | tail -n 1) 	#cpu型號
cpunum=$(( $(cat /proc/cpuinfo  | grep 'processor' | cut -d ':' -f 2 | tail -n 1) + 1 ))  	#cpu顆數
cpuspeed=$(cat /proc/cpuinfo | grep MHz | sort | head -n 1| cut -d ':' -f 2 | cut -d '.' -f 1)	# cpu speed
memtotal=$( echo "$(cat /proc/meminfo | grep 'MemTotal' | cut -d ':' -f 2 | awk '{print $1}' ) / 1024" | bc )	    #total_memory總數
memtemp1=$(cat /proc/meminfo  | grep 'MemFree' | cut -d ':' -f 2 | awk '{print $1}')	    #free_mem大小
memtemp2=$(cat /proc/meminfo  | grep   'Cached'  | sort -r | tail -n 1 | awk '{print $2}' ) #cached_mem大小
memfree=$(  echo "(${memtemp1}+${memtemp2})/1024" | bc ) 			             #可用的容量
#varcon=$(du -s /var | awk '{print $1}')
#vardir=$( echo "(${varcon})/1024" | bc)
sof=$(uname -r)
filesys_root=$(df -h / | tail -n 1 | awk '{print $4 " / " $2 }' )
filesys_jhong=$(df -h /jhong | tail -n 1 | awk '{print $4 " / " $2 }' )
#Uptime=$(/usr/bin/uptime | cut -d ',' -f 4,5,6 | cut -d ':' -f 2)
Uptime=$(/usr/bin/uptime | sed 's/^.*://g')
listtime=$(/usr/bin/uptime | awk '{print  $1" "$2" "$3" "$4" "$5" "$6}')
#filesystem=$( df -h | grep -v 'tmpfs' | grep -v 'tmpfs' | grep '^/' | awk '{print $6,"<td>："$4"/"$2"</td><tr><td>"}')
date=$(date '+%Y年%m月%d日 %T')
disk_home=$(df -h  /home | grep '^/' | awk '{print $4}')
disk_all=$(lsblk -l -o NAME,SIZE,TYPE | grep disk | grep '[0-9]*T' | awk '{print $2}' | sed 's/T//g' | sort -n| tail -n 1)
if [ "${disk_all}" != "" ]; then
	disk_all="${disk_all}T"
else
	disk_all=$(lsblk -l -o NAME,SIZE,TYPE | grep disk | grep '[0-9]*G' | awk '{print $2}' | sed 's/G//g' | sort -n| tail -n 1)
	disk_all="${disk_all}G"
fi
	
echo "<tr><td style='width:150px'><b>CPU核心數</b></td><td><b>：</b> $cpunum</td></tr> 
      <tr><td><b>CPU時脈</b></td><td><b>：</b> $cpuspeed MHz</td></tr> 
      <tr><td><b>記憶體用量</b></td><td><b>：</b> $memfree / $memtotal MB<span class="smallfont"><b> (可用容量 / 總容量)</b></span></td></tr>
      <tr><td><b>目前核心版本</b></td><td><b>：</b> $sof</td></tr> 
      <tr><td valign='top'><b>系統容量</b><br />
      <span class="smallfont">(可用/總容量)</span>
      <td><b>：</b> $filesys_root</td></tr>
      <tr><td valign='top'><b>硬碟容量</b><br />
      <span class="smallfont">(可用/總容量)</span>
      </td><td><b>：</b> ${disk_home} / ${disk_all}</td></tr>
      <tr><td><b>$filesystem</b></td></td></tr> 
      <tr><td><b>job load average</b></td><td><b>：</b>$Uptime</td></tr>"
