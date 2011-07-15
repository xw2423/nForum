<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Language" content="zh-cn" />
<meta http-equiv="Content-Type" content="text/html; charset=<{$encoding}>" />
<meta name="keywords" content="<{$keywords}>" />
<meta name="description" content="<{$description}>" />
<meta name="author" content="xw2423@BYR" />
<title><{$webTitle}></title>
<link rel="shortcut icon" type="image/x-icon" href="<{$static}><{$base}>/favicon.ico">
<{include file="css.tpl"}>
</head>

<body>
<!--header start-->
<div id="header">
	<!--top_menu start-->
	<div id="right-menu">
    	<ul>
        	<li><a href="<{$base}>#">合作交流</a></li>
            <li><a href="<{$base}>#">论坛帮助</a></li>
            <li><a href="<{$base}>/flink">友情链接</a></li>
            <li><a href="<{$base}>#">意见建议</a></li>
        </ul>
    </div>
    <!--top_menu end-->

    <!--logo start-->
    <div id="logo"><a href="<{$base}><{$home}>"><img src="<{$static}><{$base}>/img/logo.gif" /></a></div>
    <!--logo end-->

    <div id="ban_ner">
	<object codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="600" height="80"> 
	<param value="never" name="allowScriptAccess"/>
	<param value="false" name="allowFullScreen"/>
	<param value="<{$static}><{$base}>/files/swf/adv.swf" name="movie"/>
	<param value="high" name="quality"/>
	<param value="false" name="menu"/>
	<param value="borderwidth=600&borderheight=80&<{$advParam}>" name="FlashVars"/>
	<embed pluginspage="http://www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowfullscreen="false" allowscriptaccess="never" quality="high" menu="false" src="<{$base}>/files/swf/adv.swf" width="600px" height="80px" FlashVars="borderwidth=600&borderheight=80&<{$advParam}>" />
	</object>
	</div>
    <!--ban_ner end-->

</div>
<!--header end-->
<{if !($brief)}>
<{include file="left.tpl"}>
<{/if}>
<div id="main_wrap">
<div id="main">
	<!--notice start-->
	<div id="notice" class="corner">
		<div id="time">服务器时间:&ensp;<{$serverTime}></div>
    	<div id="nav"><{if $notice[0].url != ""}><a href="<{$base}><{$notice[0].url}>"><{$notice[0].text}></a><{else}><{$notice[0].text}><{/if}><{section loop=$notice name=key start=1}>&ensp;>>&ensp;<{if $notice[key].url != ""}><a href="<{$base}><{$notice[key].url}>"><{$notice[key].text}></a><{else}><{$notice[key].text}><{/if}><{/section}></div>
    </div>
    <!--notice end-->
	<div id="body" class="corner">
