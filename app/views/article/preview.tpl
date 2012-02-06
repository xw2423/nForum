<script id="tmpl_preview" type="text/template">
    <div class="a-wrap corner">
    <table class="article">
        <tr class="a-head">
            <td>
                <ul class="a-func">
                    <li><span>БъЬт:&ensp;<%=subject%></span></li>
                </ul>
            </td>
        </tr>
        <tr class="a-body">
            <td class="a-content">
                <%=content%>
            </td>
        </tr>
    </table>
    </div>
</script>
<{if isset($syntax)}><{include file="syntax_high_lighter.tpl"}><{/if}>
