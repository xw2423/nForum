<!DOCTYPE html>
<html>
<head>
<meta charset="<{$encoding}>">
<meta name="keywords" content="<{$keywords}>" />
<meta name="description" content="<{$description}>" />
<meta name="author" content="xw2423@BYR" />
<title><{$webTitle}></title>
<link rel="shortcut icon" type="image/x-icon" href="<{$static}><{$base}>/favicon.ico">
<{if !$islogin && $preindex}>
<script>
if(!location.hash.match(/^#.+$/)) location.href='index';
</script>
<{/if}>
<!--[if lt IE 9]>
<script src="<{$static}><{$base}>/js/html5.js"></script>
<![endif]-->
<{include file="css.tpl"}>
</head>

<body>

<!--header start-->
<header id="top_head">
    <!--top-menu start-->
    <aside id="top_menu">
        <ul>
            <li><a href="<{$base}>#">合作交流</a></li>
            <li><a href="<{$base}>#">论坛帮助</a></li>
            <li><a href="<{$base}>/flink">友情链接</a></li>
            <li><a href="<{$base}>#">意见建议</a></li>
        </ul>
    </aside>
    <!--top-menu end-->

    <!--logo start-->
    <figure id="top_logo">
        <a href="<{$base}><{$home}>">
            <img src="<{$static}><{$base}>/img/logo.gif" />
        </a>
    </figure>
    <!--logo end-->

    <!--ban_ner start-->
    <article id="ban_ner">
        <div id="ban_ner_wrapper">
            <ul>
<{foreach from=$banner_adv item=item}>
                <li><a href="<{$item.url|default:"javascript:void(0);"}>"<{if $item.url}> target="_blank"<{/if}>><img src="<{$static}><{$base}><{$item.file}>" alt="<{$item.remark}>" width="600px" height="80px" /></a></li>
<{/foreach}>
            </ul>
        </div>
    </article>
    <!--ban_ner end-->

</header>
<!--header end-->

<!--menu start-->
<aside id="menu" class="m-hide">

    <!--login start-->
    <section id="u_login" class="corner">
<script id="tmpl_u_login" type="text/template">
        <form action="<{$base}>/login<{if isset($from)}>?from=<{$from}><{/if}>" method="post" id="u_login_form">
        <div class="u-login-input"><span>帐号:</span><input type="text" id="u_login_id" class="input-text input" name="id"/></div>
        <div class="u-login-input"><span>密码:</span><input type="password" id="u_login_passwd" class="input-text input" name="passwd"/></div>
        <div class="u-login-check"><input type="checkbox" id="u_login_cookie" name="CookieDate" value="2"/><label for="u_login_cookie">下次自动登录</label></div>
        <div class="u-login-op">
            <input type="submit" id="u_login_submit" class="submit" value="登录" /><input class="submit" type="button" value="注册" id="u_login_reg"/>
        </div>
        </form>
</script>
<script id="tmpl_u_login_info" type="text/template">
        <div class="u-login-id"><samp class="ico-pos-cdot"></samp>欢迎<a href="<{$base}>/user/query/<%=id%>" title="<%=id%>"><%=id.length<11?id:(id.substr(0,10)+'...')%></a></div>
        <ul class="u-login-list">
            <li><a href="<{$base}>/mail" id="m_inbox">我的收件箱
<%if (full_mail){%><span class="new_mail">(满!)</span><%}else if(new_mail){%><span class="new_mail">(新)</span><%}%></a>
            </li>
<%if(typeof new_at !== 'undefined' && false !== new_at){%>
            <li><a href="<{$base}>/refer/at" id="m_at">@我的文章</a>
<%if(new_at>0){%><span class="new_mail">(<%=new_at%>)</span><%}%></a>
            </li>
<%}%>
<%if(typeof new_reply !== 'undefined' && false !== new_reply){%>
            <li><a href="<{$base}>/refer/reply" id="m_reply">回复我的文章</a>
<%if(new_reply>0){%><span class="new_mail">(<%=new_reply%>)</span><%}%></a>
            </li>
<%}%>
            <li><a href="<{$base}>/fav" id="u_fav">我的收藏夹</a></li>
            <li><a href="<{$base}>/widget/add">个性首页设置</a></li>
            <li><a href="javascript:void(0)" id="u_login_out">退出登录</a></li>
        </ul>
</script>
    </section>
    <!--login end-->

    <div id="left_line">
        <samp class="ico-pos-hide"></samp>
    </div>

    <!--nav start -->
    <nav id="xlist" class="corner">
<script id="tmpl_left_nav" type="text/template">
        <ul>
            <li class="slist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">全部讨论区</a></span>
                <ul class="x-child ajax"><li>{url:<{$base}>/slist.json?uid=<%=id%>&root=list-section}</li>
                </ul>
            </li>
<%if(is_login){ %>
            <li class="flist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0);">我的收藏夹</a></span>
                <ul id="list-favor" class="x-child ajax"><li>{url:<{$base}>/flist.json?uid=<%=id%>&root=list-favor}</li></ul>
            </li>
<%}%>
            <li class="clist">
                <span class="x-folder"><span class="toggler"></span><a href="javascript:void(0)">控制面板</a></span>
                <ul class="x-child" id="list-control">
            <%if(is_login){%>
                <%if(!is_register){%>
                    <li class="leaf"><span class="text"><a href="<{$base}>/reg/form"><samp class="ico-pos-dot"></samp>填写注册单</a></span></li>
                <%}%>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/info" ><samp class="ico-pos-dot"></samp>基本资料修改</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/passwd" ><samp class="ico-pos-dot"></samp>昵称密码修改</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/custom" ><samp class="ico-pos-dot"></samp>用户自定义参数</a></span></li>
<{if $refer}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/refer" ><samp class="ico-pos-dot"></samp>文章提醒</a></span></li>
<{/if}>
                    <li class="leaf"><span class="text"><a href="<{$base}>/friend" ><samp class="ico-pos-dot"></samp>好友列表</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/fav" ><samp class="ico-pos-dot"></samp>收藏版面</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/online" ><samp class="ico-pos-dot"></samp>在线用户</a></span></li>
            <%}%>
                    <li class="leaf"><span class="text"><a href="<{$base}>/user/query" id="u_query"><samp class="ico-pos-dot"></samp>查询用户</a></span></li>
                    <li class="leaf"><span class="text"><a href="<{$base}>/s" ><samp class="ico-pos-dot"></samp>搜索文章</a></span></li>
                </ul>
            </li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/vote">投票系统</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="<{$base}>/elite/path">精华区</a></span></li>
            <li><span class="x-leaf"><span class="toggler"></span><a href="telnet://#" target="_blank">Telnet登录</a></span></li>
            <li><span class="x-leaf x-search"><span class="toggler"></span><input type="text" class="input-text" placeholder="搜索讨论区" id="b_search" x-webkit-speech lang="zh-CN"/></span></li>
        </ul>
</script>
    </nav>
    <!--nav list end-->

    <section id="left_adv">
<{foreach from=$left_adv item=item}>
        <a href="<{$item.url|default:"javascript:void(0);"}>"<{if $item.url}> target="_blank"<{/if}>><img src="<{$static}><{$base}><{$item.file}>" /></a>
<{/foreach}>
    </section>
</aside>
<!--menu end-->

<!--main start-->
<section id="main" class="corner">

    <!--notice start-->
    <nav id="notice" class="corner">
        <div id="notice_nav"><{if $notice[0].url != ""}><a href="<{$base}><{$notice[0].url}>"><{$notice[0].text}></a><{else}><{$notice[0].text}><{/if}><{section loop=$notice name=key start=1}>&ensp;>>&ensp;<{if $notice[key].url != ""}><a href="<{$base}><{$notice[key].url}>"><{$notice[key].text}></a><{else}><{$notice[key].text}><{/if}><{/section}></div>
    </nav>
    <!--notice end-->

    <!--body start-->
    <section id="body" class="corner">
    </section>
    <!--body end-->

</section>
<!--main end-->
<div class="clearfix" style="width:100%"></div>
<!--footer start-->
<footer id="bot_foot">
    <figure id="bot_logo">
        <a href="<{$base}><{$home}>">
            <img src="<{$static}><{$base}>/img/logo_footer.gif" />
        </a>
    </figure>
    <aside id='bot_info'>
        当前论坛上总共有<span class="c-total"><{$webTotal}></span>人在线，其中注册用户<span class="c-user"><{$webUser}></span>人，访客<span class="c-guest"><{$webGuest}></span>人。<br />
        powered by BYR-Team<span class="copyright">&copy;</span>2009-<{$smarty.now|date_format:"%Y"}>.<br />
        all rights reserved
    </aside>
</footer>
<figure id="nforum_tips">加载中..</figure>
<{include file="user/query.tpl"}>
<!--footer end-->
<{include file="script.tpl"}>
<script>front_startup()</script>
</body>
</html>
