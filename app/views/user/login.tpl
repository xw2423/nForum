<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="登录论坛"}>
        <div class="b-content corner">
<{if isset($from)}>
			<form method="post" action="<{$base}>/login?from=<{$from}>">
            <ul class="l-error corner">
            	<li><samp class="ico-pos-dot"></samp>您是否仔细阅读了帮助文件，可能您还没有登录或者不具有使用当前功能的权限。</li>
            </ul>
<{else}>
			<form method="post" action="<{$base}>/login">
<{/if}>
            <ul class="l-area">
                <li><label for="id" class="t-label">请输入您的用户名:</label><input class="input-text input" type="text" id="id" name="id" tabindex="1"/><a href="<{$base}>/reg">没有注册?</a></li>
                <li><label for="pwd" class="t-label">请输入您的密码:</label><input class="input-text input" type="password" id="pwd" name="passwd" tabindex="2"/><a href="<{$base}>/reset">忘记密码?</a></li>
                <li>
                    <label for="" class="t-label">cookie选项：</label>
                    <input class="radio" type="radio" checked="checked" name="CookieDate" id="0" value="0"/><label class="r-label" for="1">不保存</label>
                    <input class="radio" type="radio" name="CookieDate" id="2"  value="1"/><label class="r-label" for="2">保存一天</label>
                    <input class="radio" type="radio" name="CookieDate" id="3"  value="2"/><label class="r-label" for="3">保存一月</label>
                    <input class="radio" type="radio" name="CookieDate" id="4"  value="3"/><label class="r-label" for="4">保存一年</label>
                </li>
                <li class="op"><input class="button" type="submit" value="登录" /><input class="button" type="button" value="返回上一页"  onclick="javascript:history.go(-1)"/></li>
            </ul>            
            </form>
        </div>   
<{include file="footer.tpl"}>
