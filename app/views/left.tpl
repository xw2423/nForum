<!--menu start-->
<div id="menu" class="m-hide">
    <!--login start-->
    <div id="u_login_wrap">
<{if !($islogin)}>
	<div class="u-login">
		<form class="user_login_form" action="<{$base}>/login<{if isset($from)}>?from=<{$from}><{/if}>" method="post">
		<div><span>帐号:</span><input type="text" id="id" class="input-text input" name="id"/></div>
		<div><span>密码:</span><input type="password" id="pwd" class="input-text input" name="passwd"/></div>
		<div class="l-check"><input type="checkbox" id="c_auto" name="CookieDate" value="2"/><label for="c_auto">下次自动登录</label></div>
		<div class="b-op">
			<input type="submit" id="bb_login" class="submit" value="登录" /><input class="submit" type="button" value="注册" id="bb_reg"/>
		</div>
        </form>  
    </div>
<{else}><{if $mailInfo.newmail}>
	<bgsound src="<{$base}>/files/audio/mail.wav" /><{/if}>
    <div class="u-login-info">
    	<div><samp class="ico-pos-cdot"></samp>欢迎<a href="<{$base}>/user/query/<{$id}>" title="<{$id}>"><{$id|truncate:11:"..."}></a></div>
        <ul>
            <li><a href="<{$base}>/mail">我的收件箱<{if $mailInfo.full}><span class="new_mail">(满!)</span><{elseif $mailInfo.newmail}><span class="new_mail">(新)</span><{/if}></a></li>
            <!--<li><a href="#">我的个人博客</a></li>-->
            <li><a href="<{$base}>/fav">我的收藏夹</a></li>
        	<li><a href="<{$base}>/widget/add">个性首页设置</a></li>
        	<!--<li><a href="#">论坛主题设置</a></li>-->
            <li><a href="<{$base}>/logout">退出登录</a></li>
        </ul>
    </div>
<{/if}>
    </div>
    <!--login end-->
	<div id="left-line">
		<samp class="ico-pos-hide"></samp>
	</div>
    <!--function list start -->
    <div id="xlist_wrap">
	<div id="xlist">
    	<ul>
            <li class="slist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">全部讨论区</a></span>
                <ul class="x-child ajax"><li>{url:<{$base}>/slist?uid=<{$id}>&root=list-section}</li>
                </ul>
            </li>
<{if $islogin}>
            <li class="flist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">我的收藏夹</a></span>
                <ul id="list-favor" class="x-child ajax"><li>{url:<{$base}>/flist?uid=<{$id}>&root=list-favor}</li></ul>
            </li>
<{/if}>
            <li class="clist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0)">控制面板</a></span>
                <ul class="x-child" id="list-control">
            <{if $islogin}>
                <{if !$isReg}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/reg/form"><samp class="ico-pos-dot"></samp>填写注册单</a></span></li>
                <{/if}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/info" ><samp class="ico-pos-dot"></samp>基本资料修改</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/passwd" ><samp class="ico-pos-dot"></samp>昵称密码修改</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/custom" ><samp class="ico-pos-dot"></samp>用户自定义参数</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/friend" ><samp class="ico-pos-dot"></samp>好友列表</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/fav" ><samp class="ico-pos-dot"></samp>收藏夹管理</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/online" ><samp class="ico-pos-dot"></samp>在线用户</a></span></li>
            <{/if}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/query" ><samp class="ico-pos-dot"></samp>查询用户</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/s" ><samp class="ico-pos-dot"></samp>搜索文章</a></span></li>
                </ul>
            </li>
			<li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/vote">投票系统</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/elite/path">精华区</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="telnet://#">Telnet登录</a></span></li>
            <li><span class="x-leaf x-search"><span class="toggler"></span><input type="text" class="input-text" value="搜索讨论区" id="b_search"/></span></li>
        </ul>
    </div>
    </div>
    <!--function list end-->
	<div id="adv">
<{foreach from=$advs item=item}>
<{if empty($item.url)}>
		<a href="javascript:void(0);"><img src="<{$static}><{$base}><{$item.file}>" /></a>
<{else}>
		<a href="<{$item.url}>" target="_blank"><img src="<{$static}><{$base}><{$item.file}>" /></a>
<{/if}>
<{/foreach}>
	</div>
</div>
<!--menu end-->
