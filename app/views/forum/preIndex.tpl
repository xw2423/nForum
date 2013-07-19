<!DOCTYPE html>
<html>
<head>
<meta charset="<{$encoding}>">
<meta name="keywords" content="<{$keywords}>" />
<meta name="description" content="<{$description}>" />
<meta name="author" content="xw2423@BYR" />
<title><{$webTitle}></title>
<link rel="shortcut icon" type="image/x-icon" href="<{$static}><{$base}>/favicon.ico">
<!--[if lt IE 9]>
<script src="<{$static}><{$base}>/js/html5.js"></script>
<![endif]-->
<{include file="css.tpl"}>
</head>
<body>
<section id="wrap">
    <figure id="preImg">
    <{if !empty($preimg)}>
    <a href="<{$preimg.url}>" ><img src="<{$static}><{$base}><{$preimg.file}>" alt="点击进入<{$siteName}>"/></a>
    <{/if}>
    </figure>

    <div id="login">
        <form id="f_login" method="post" action="<{$base}>/login">
        <span>用户名:</span>
        <input name="id" type="text" class="input-text"  id="id" size="10" onmouseover=this.focus() tabindex="1"/>
        <span>密码:</span>
        <input name="passwd" type="password" class="input-text" id="pwd" size="10" onmouseover=this.focus() tabindex="2" />
        <select name="mode" id="s-mode">
        <option value="0">nForum</option>
        </select>
        <select name="CookieDate">
        <option value="0">不保存</option>
        <option value="1">保存一天</option>
        <option value="2">保存一月</option>
        <option value="3">保存一年</option>
        </select>
        <input type="submit" id="b_login" class="submit" value="登录"/>
        <input type="button" id="b_guest" class="submit" value="游客"/>
        <input type="button" id="b_reg" class="submit" value="注册"/>
        </form>
    </div>
    <div id="b_adv">
        <ul>
        <{foreach from=$preadv item=item}>
        <{if empty($item.url)}>
        <li><a href="javascript:void(0);"><img src="<{$static}><{$base}><{$item.file}>"></a></li>
        <{else}>
        <li><a href="<{$item.url}>" target="_blank"><img src="<{$static}><{$base}><{$item.file}>"></a></li>
        <{/if}>
        <{/foreach}>
        </ul>
    </div>
</section>
<!--footer start-->
<footer id="bot_foot">
    <figure id="bot_logo">
        <a href="<{$base}><{$home}>">
            <img src="<{$static}><{$base}>/img/logo_footer.gif" />
        </a>
    </figure>
    <aside id='bot_info'>
        当前论坛上总共有<span class="c-total"><{$webTotal}></span>人在线，其中注册用户<span class="c-user"><{$webUser}></span>人，访客<span class="c-guest"><{$webGuest}></span>人。<br />
        powered by BYR-Team&copy;2009-<{$smarty.now|date_format:"%Y"}>.<br />
        all rights reserved
    </aside>
</footer>
<!--footer end-->
<{include file="script.tpl"}>
</body>
</html>
