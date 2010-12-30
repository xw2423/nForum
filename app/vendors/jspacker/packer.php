<?php
// you can pass this script to PHP CLI to convert your file.

// adapt these 2 paths to your files.
//$src = 'test-src.js';
//$out = 'test.js';

// or uncomment these lines to use the argc and argv passed by CLI :
if ($argc >= 3) {
    $src = $argv[1];
    $out = $argv[2];
} else {
    echo 'you must specify  a source file and a result filename',"\n";
    echo 'example :', "\n", 'php example-file.php myScript-src.js myPackedScript.js',"\n";
    return;
}
chdir(BBS_CWD);
require 'class.JavaScriptPacker.php';

$script = file_get_contents($src);

$t1 = microtime(true);

//$pattern = array("/([".chr(0xa1)."-".chr(0xff)."]{2})+/", "/([".chr(0xa1)."-".chr(0xff)."]{2})+/e");
//$replace = array("'+encodeURI('\\0')+'", "urlencode(iconv('gbk', 'utf-8', '\\0'))");
//$script = preg_replace($pattern, $replace, $script);
//echo $script;
//exit();
$packer = new JavaScriptPacker($script, 'None', true, false);
$packed = $packer->pack();

$t2 = microtime(true);
$time = sprintf('%.4f', ($t2 - $t1) );
echo 'script ', $src, ' packed in ' , $out, ', in ', $time, ' s.', "\n";

file_put_contents($out, $packed);
?>
