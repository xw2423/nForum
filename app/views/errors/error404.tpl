<html>
<head>
<style>
a{color:#FFF;}
</style>
</head>
<body style="background-color:#009;color:#FFF;font-weight:bold;*text-align:center">
<div style="width:640px;margin:auto;*text-align:left";>
	<div style="margin-top:100px;text-align:center;">
		<span style="background-color:#999;padding:2px 15px;color:#009"><{$siteName}></span>
	</div>
	<div style="margin-top:30px;">
在您访问此链接时出现了一个异常，以至于当前请求中止，可能的原因是该链接不存在或者是您没有足够的权限！
<br/ ><br/ >
*&nbsp;&nbsp;<a href="javascript:window.opener=null;window.close();">单击此处</a> 关闭当前窗口。
<br/ >
*&nbsp;&nbsp;<a href="<{$domain}><{$base}>">单击此处</a> 返回<{$siteName}>，你将会丢失本次请求中所有没有保存的数据。
	</div>
	<div style="text-align:center;margin-top:30px;">
<a href="javascript:history.go(-1);" style="text-align:center">单击此处</a> 返回&nbsp;_
	</div>
</div>
</body>
