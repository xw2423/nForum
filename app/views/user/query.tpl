<script id="tmpl_user" type="text/template">
<section class="u-query">
    <%if(id){%>
    <header class="u-name">
        <span><%=id%></span>
        <%if(session_login){%>
        <a href="<{$base}>/mail/send?id=<%=id%>" id="u_query_mail">发问候信</a>|<a href="javascript:void(0)" id="u_query_add">加为好友</a>
        <%}%>
    </header>
    <article class="u-info">
        <header>基本信息</header>
        <figure>
        <img src="<%-face_url%>"<%if(face_width != 0){%> width="<%=face_width%>px"<%}%><%if(face_height != 0){%> height="<%=face_height%>px"<%}%> />
        </figure>
        <dl>
            <dt>昵 称：</dt>
            <dd><%-user_name%></dd>
        <%if(id == session_id || !is_hide || session_is_admin){%>
            <dt>性 别：</dt>
            <dd><%if(gender=='m'){%>男生<%}else{%>女生<%}%></dd>
            <dt>星 座：</dt>
            <dd><%=astro%></dd>
        <%}%>
            <dt>QQ：</dt>
            <dd><%-qq%></dd>
            <dt>MSN：</dt>
            <dd><%-msn%></dd>
            <dt>主 页：</dt>
            <dd><%-home_page%></dd>
        </dl>
    </article>
    <div class="clearfix"></div>
    <article class="u-info u-detail">
        <header>论坛属性</header>
        <dl class="">
            <dt>论坛等级：</dt>
            <dd><%=level%></dd>
            <dt>帖子总数：</dt>
            <dd><%=post_count%>篇</dd>
        <%if(id == session_id || session_is_admin){%>
            <dt>登陆次数：</dt>
            <dd><%=login_count%></dd>
        <%}%>
            <dt>生命力：</dt>
            <dd><%=life%></dd>
        <%if(id == session_id || session_is_admin){%>
            <dt>注册时间：</dt>
            <dd><%=first_login_time%></dd>
        <%}%>
            <dt>上次登录：</dt>
            <dd><%=last_login_time%></dd>
            <dt>最后访问IP：</dt>
            <dd><%=last_login_ip%></dd>
            <dt>当前状态：</dt>
            <dd><%=status%></dd>
        </dl>
    </article>
    <%}%>
    <footer class="u-search">
        <form method="get">
        <label>查询用户:</label>
        <input class="input-text" id="u_search_u"type="text" value="" />
        <input class="button" id="u_query_search" type="submit" value="查询" />
        </form>
    </footer>
</section>
</script>
