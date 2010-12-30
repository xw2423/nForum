<{include file="header.tpl"}>
	<{include file="s_nav.tpl" nav_left="用户信息"}>
        <div class="b-content corner">
<{if !isset($noid)}>
        	<h5 class="u-name">
				<{$uid}>
				<{if $islogin}>
				<a href="<{$base}>/mail/send?id=<{$uid}>">发问候信</a>|<a href="<{$base}>/friend/add?id=<{$uid}>">加为好友</a>
				<{/if}>
			</h5>
			<h5 class="u-title">基本信息</h5>
			<div id="u-img">
            <img src="<{$static}><{$base}><{$furl}>" <{if $fwidth != ""}>width="<{$fwidth}>px"<{/if}> <{if $fheight != ""}>height="<{$fheight}>px"<{/if}> />
			</div>
            <dl class="u-info">
            	<dt>昵 称：</dt>
				<dd><{$name}></dd>
<{if !($hide) || $isAdmin}>
            	<dt>性 别：</dt>
				<dd><{$gender}></dd>
                <dt>星 座：</dt>
				<dd><{$astro}></dd>
<{/if}>
                <dt>QQ：</dt>
				<dd><{$qq}></dd>
                <dt>MSN：</dt>
				<dd><{$msn}></dd>
                <dt>主 页：</dt>
				<dd><{$homepage}></dd>
            </dl>
            <h5 class="u-title">论坛属性</h5>	
            <dl class="u-info u-detail">
            	<dt>论坛等级：</dt>
				<dd><{$level}></dd>
                <dt>帖子总数：</dt>
				<dd><{$postNum}>篇</dd>
<{if $me || $isAdmin}>
                <dt>登陆次数：</dt>
				<dd><{$loginNum}></dd>
<{/if}>
                <dt>生命力：</dt>
				<dd><{$life}></dd>
<{if $me || $isAdmin}>
                <dt>注册时间：</dt>
				<dd><{$first}></dd>
<{/if}>
                <dt>上次登录：</dt>
				<dd><{$lastTime}></dd>
                <dt>最后访问IP：</dt>
				<dd><{$lastIP}></dd>
                <dt>当前状态：</dt>
				<dd><{$status}></dd>
            </dl>
<{/if}>
            <h5 class="search_user">
				<form method="get" action="/user/query" id="f_search" >
				<span>查询用户：</span>
				<input class="input_search input-text"type="text" id="s_name" value="" />
				<input class="search_sub button" type="submit" id="s_btn" value="查询" />
				</form>
			</h5>
    	</div>
<{include file="footer.tpl"}>
