    <{capture name=n_right}><a href="<{$base}>/elite/path<{if !empty($parent)}>?v=<{$parent}><{/if}>" style="color:#fff;font-size:12px" >返回</a><{/capture}>
    
    <{include file="s_nav.tpl" nav_left="精华区文章阅读" nav_right=$smarty.capture.n_right}>
    <div class="a-wrap corner">
    <table class="article">
        <tr class="a-head">
            <td>
                <ul class="a-func">
                    <li><span>标题:&ensp;<{$subject}></span></li>
                </ul>
            </td>
        </tr>
        <tr class="a-body">
            <td class="a-content">
                <{$content}>
            </td>
        </tr>
    </table>
    </div>
