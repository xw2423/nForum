<script id="tmpl_article_single" type="text/template">
<div class="m-op">
    <ul class="m-func"> 
        <li><samp class="ico-pos-reply"></samp><a href="article/<%=board_name%>/post/<%=id%>" class="a-post">回复</a></li> 
<%if(id==group_id){%>
        <li><samp class="ico-pos-template"></samp><a href="article/<%=board_name%>/tmpl?id=<%=id%>" class="a-post">模版回复</a></li> 
<%}%>
        <li><samp class="ico-pos-forward"></samp><a href="article/<%=board_name%>/ajax_forward/<%=id%>.json" class="a-func-forward">转寄</a></li> 
        <li><samp class="ico-pos-search"></samp><a href="s/article?b=<%=board_name%>&au=<%=user.id||user%>" class="a-close">搜索</a></li>
<%if(is_admin){%>
        <li><samp class="ico-pos-edit"></samp><a href="article/<%=board_name%>/edit/<%=id%>" class="a-close">编辑</a></li>
        <li><samp class="ico-pos-del"></samp><a href="article/<%=board_name%>/ajax_delete/<%=id%>.json" class="a-func-del">删除</a></li> 
<%}%>
        <li><samp class="ico-pos-edit"></samp><a href="article/<%=board_name%>/post" class="a-post">发文</a></li>
        <li><samp class="ico-pos-query"></samp><a href="user/query/<%=user.id||user%>">作者信息</a></li> 
    </ul>
</div>
<div class="m-content">
<%=content%>
</div>
<div class="m-op">
    <ul class="m-func"> 
<%if(previous_id){%>
        <li><a href="article/<%=board_name%>/ajax_single/<%=previous_id%>.json" class="a-single">上一篇</a></li> 
<%}%>
<%if(next_id){%>
        <li><a href="article/<%=board_name%>/ajax_single/<%=next_id%>.json" class="a-single">下一篇</a></li> 
<%}%>
<%if(threads_previous_id){%>
        <li><a href="article/<%=board_name%>/ajax_single/<%=threads_previous_id%>.json" class="a-single">同主题上一篇</a></li> 
<%}%>
<%if(threads_next_id){%>
        <li><a href="article/<%=board_name%>/ajax_single/<%=threads_next_id%>.json" class="a-single">同主题下一篇</a></li> 
<%}%>
        <li><a href="article/<%=board_name%>/<%=group_id%>?s=<%=id%>" class="a-close">展开</a></li>
        <li><a href="article/<%=board_name%>/ajax_single/<%=group_id%>.json" class="a-single">楼主</a></li> 
        <li><a href="article/<%=board_name%>/<%=group_id%>" class="a-close">同主题展开</a></li>
        <li><a href="article/<%=board_name%>/ajax_single/<%=reply_id%>.json" class="a-single">溯源</a></li>
    </ul>
</div>
</script>
<{include file="article/forward.tpl"}>
<{if isset($syntax)}><{include file="syntax_high_lighter.tpl"}><{/if}>
