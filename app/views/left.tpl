<!--menu start-->
<aside id="menu" class="m-hide">

    <!--login start-->
    <section id="u_login" class="corner">
<{if !($islogin)}>
        <form action="<{$base}>/login<{if isset($from)}>?from=<{$from}><{/if}>" method="post">
        <div class="u-login-input"><span>帐号:</span><input type="text" id="u_login_id" class="input-text input" name="id"/></div>
        <div class="u-login-input"><span>密码:</span><input type="password" id="u_login_passwd" class="input-text input" name="passwd"/></div>
        <div class="u-login-check"><input type="checkbox" id="u_login_cookie" name="CookieDate" value="2"/><label for="c_auto">下次自动登录</label></div>
        <div class="u-login-op">
            <input type="submit" id="u_login_submit" class="submit" value="登录" /><input class="submit" type="button" value="注册" id="u_login_reg"/>
        </div>
        </form>
<{else}>
        <div class="u-login-id"><samp class="ico-pos-cdot"></samp>欢迎<a href="<{$base}>/user/query/<{$id}>" title="<{$id}>"><{$id|truncate:11:"..."}></a></div>
        <ul class="u-login-list">
            <li><a href="<{$base}>/mail">我的收件箱</a></li>
            <li><a href="<{$base}>/fav">我的收藏夹</a></li>
            <li><a href="<{$base}>/widget/add">个性首页设置</a></li>
            <!--<li><a href="#">论坛主题设置</a></li>-->
            <li><a href="<{$base}>/logout">退出登录</a></li>
        </ul>
<{/if}>
    </section>
    <!--login end-->

    <div id="left-line">
        <samp class="ico-pos-hide"></samp>
    </div>

    <!--nav start -->
    <nav id="xlist" class="corner">
        <ul>
            <li class="slist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">全部讨论区</a></span>
                <ul class="x-child ajax"><li>{url:<{$base}>/slist.json?uid=<{$id}>&root=list-section}</li>
                </ul>
            </li>
<{if $islogin}>
            <li class="flist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">我的收藏夹</a></span>
                <ul id="list-favor" class="x-child ajax"><li>{url:<{$base}>/flist.json?uid=<{$id}>&root=list-favor}</li></ul>
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
    </nav>
    <!--nav list end-->

    <section id="adv">
    </section>
</aside>
<!--menu end-->
