#!/bin/bash
if [ $# -ne 2 ]
then
	echo "usage: $0 CAKELIB KBS_SOURCE"
	exit
fi
cakelib="$1"
if [ ! -f "$cakelib/libs/error.php"  -o ! -f "$cakelib/dispatcher.php" -o ! -f "$cakelib/console/cake.php" ]
then
    echo "error:cakelib directory wrong"
    exit
fi
patch -s -d $cakelib -p 2 < patch/cake.patch

kbslib="$2"
if [ ! -d "$kbslib/php" ]
then
    echo "error:kbs source directory wrong"
    exit
fi
patch -s -d $kbslib -p 0 < patch/kbs.patch

tmpdir="tmp/cache tmp/cache/models tmp/cache/persistent tmp/cache/asset tmp/cache/nforum tmp/compile tmp/logs www/files/adv www/uploadFace"
for dir in $tmpdir
do
	if [ ! -d app/$dir ]
	then
		mkdir app/$dir
	fi  
	chmod 777 app/$dir
done

echo 'install successfully!'
