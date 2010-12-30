<{include file="script.tpl"}>
<style>
*{ margin:0px; padding:0px; }
a { color:#6595D6; text-decoration:none; }
a:hover { color:#6595D6; text-decoration:underline; }
body{ font-family:Arial,Verdana,Sans-Serif; font-size:0.8em; }
li{list-style:none}
</style>
<div id="result">
<{$msg}>,&nbsp;<a href="javascript:window.location.href='<{$base}>/att/upload/<{$bName}><{$postUrl|default:""}>'" >点击此继续上传</a>
</div>
<{if isset($num)}>
<script type="text/javascript" >
var txt = parent.document.getElementById('ta_content');
txt.value += "[upload=<{$num}>][/upload]";
<{if isset($exif)}>
txt.value += "\n<{$exif}>";
<{/if}>
</script>
<{/if}>
