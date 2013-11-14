<style type="text/css">
.error-wrap{
    width:100%;
    background-color:#009;
    margin-top:3px;
    text-align:center;
}
.error-main{
    width:640px;
    color:#FFF;
    font-weight:bold;
    font-size:14px;
    margin:auto;
    text-align:left;
    height:200px;
    padding:80px 0
}
.error-main a{color:#FFF;}
</style>
<div class="error-wrap corner">
<div class="error-main">
    <div style="text-align:center;">
        <span style="background-color:#999;padding:2px 15px;color:#009"><{$siteName}></span>
    </div>
    <div style="margin-top:30px;">
在您访问此链接时出现了一个异常，以至于当前请求中止，可能的原因是该链接不存在或者是您没有足够的权限！
<br/ ><br/ >
*&emsp;<a href="javascript:window.opener=null;window.close();">单击此处</a> 关闭当前窗口。
<br/ >
*&emsp;<a href="<{$domain}><{$base}><{$home}>">单击此处</a> 返回&ensp;<u><{$siteName}></u>&ensp;，你将会丢失本次请求中所有没有保存的数据。
    </div>
    <div style="text-align:center;margin-top:30px;">
<a href="javascript:history.go(-1);" style="text-align:center">单击此处</a> 返回&ensp;_
    </div>
</div>
</div>
