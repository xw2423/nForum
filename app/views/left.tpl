<!--menu start-->
<div id="menu" class="m-hide">
    <!--login start-->
<{if !($islogin)}>
	<div class="u-login corner">
		<form class="user_login_form" action="<{$base}>/login<{if isset($from)}>?from=<{$from}><{/if}>" method="post">
		<div><span>帐号:</span><input type="text" id="id" class="input-text input" name="id"/></div>
		<div><span>密码:</span><input type="password" id="pwd" class="input-text input" name="passwd"/></div>
		<div class="l-check"><input type="checkbox" id="c_auto" name="CookieDate" value="2"/><label for="c_auto">下次自动登录</label></div>
		<div class="b-op">
			<input type="submit" id="bb_login" class="submit" value="登录" /><input class="submit" type="button" value="注册" id="bb_reg"/>
		</div>
        </form>  
    </div>
<{else}><{if $newNum != 0}>
	<bgsound src="<{$base}>/files/audio/mail.wav" /><{/if}>
    <div class="u-login-info corner">
    	<div><samp class="ico-pos-cdot"></samp>欢迎<a href="<{$base}>/user/query/<{$id}>" title="<{$id}>"><{$id|truncate:11:"..."}></a></div>
        <ul>
        	<li><a href="<{$base}>/mail">我的收件箱<{if $newNum != 0}><span class="new_mail">(<{$newNum}>新)</span><{/if}></a></li>
            <!--<li><a href="#">我的个人博客</a></li>-->
            <li><a href="<{$base}>/fav">我的收藏夹</a></li>
        	<li><a href="<{$base}>/widget/add">个性首页设置</a></li>
        	<!--<li><a href="#">论坛主题设置</a></li>-->
            <li><a href="<{$base}>/logout">退出登录</a></li>
        </ul>
    </div>
<{/if}>
    <!--login end-->
	<div id="left-line">
		<samp class="ico-pos-hide"></samp>
	</div>
    <!--function list start -->
	<div id="xlist" class="corner">
    	<ul>
			<li class="has_child"> <a href="javascript:void(0);" class="xlist-a"><samp class="ico-pos-tag-off"></samp>全部讨论区</a><ul  id="list-section" class="child_list xtree"> </ul> </li>
<{if $islogin}>
            <li class="has_child"><a href="javascript:void(0);" class="xlist-a"><samp class="ico-pos-tag-off"></samp>我的收藏夹</a><ul id="list-favor" class="child_list xtree"></ul></li>
<{/if}>
            <li class="has_child"><a href="javascript:void(0)" class="xlist-a"><samp class="ico-pos-tag-off"></samp>控制面板</a><ul class="child_list" id="list-control"><{if $islogin}><{if !$isReg}><li><a href="<{$base}>/reg/form" class="xlist-a"><samp class="ico-pos-dot"></samp>填写注册单</a></li><{/if}><li><a href="<{$base}>/user/info" class="xlist-a"><samp class="ico-pos-dot"></samp>基本资料修改</a></li><li><a href="<{$base}>/user/passwd" class="xlist-a"><samp class="ico-pos-dot"></samp>昵称密码修改</a></li><li><a href="<{$base}>/user/custom" class="xlist-a"><samp class="ico-pos-dot"></samp>用户自定义参数</a></li><li><a href="<{$base}>/friend" class="xlist-a"><samp class="ico-pos-dot"></samp>好友列表</a></li><li><a href="<{$base}>/fav" class="xlist-a"><samp class="ico-pos-dot"></samp>收藏夹管理</a></li><li><a href="<{$base}>/online" class="xlist-a"><samp class="ico-pos-dot"></samp>在线用户</a></li><{/if}><li><a href="<{$base}>/user/query" class="xlist-a"><samp class="ico-pos-dot"></samp>查询用户</a></li><li><a href="<{$base}>/s" class="xlist-a"><samp class="ico-pos-dot"></samp>搜索文章</a></li></ul></li>
			<li class="no_child"><a href="<{$base}>/vote" class="xlist-a"><samp class="ico-pos-cdot"></samp>投票系统</a></li><li class="no_child"><a href="<{$base}>/elite/path" class="xlist-a"><samp class="ico-pos-cdot"></samp>精华区</a></li><li class="no_child"><a href="telnet://#" class="xlist-a"><samp class="ico-pos-cdot"></samp>Telnet登录</a></li><li class="no_child search"><samp class="ico-pos-cdot"></samp><input type="text" class="input-text" value="搜索讨论区" id="b_search"/></li>
        </ul>
    </div>
    <!--function list end-->
	<div id="adv">
<{foreach from=$advs item=item}>
		<a href="<{$item.url}>"><img src="<{$base}><{$item.path}>" /></a>
<{/foreach}>
	</div>
</div>
<!--menu end-->
