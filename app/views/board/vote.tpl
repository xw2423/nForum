    <{include file="s_nav.tpl" nav_left="版内投票"}>
        <div class="b-content corner">
        <div class="vote" style="padding:10px;">
            <table class="m-table" style="text-align:center">
            <tr>
                <th style="width:30px">序号</th>
                <th style="width:60px">类型</th>
                <th style="width:auto">标题 </th>
                <th style="width:150px">发起时间</th>
                <th style="width:60px">持续时间</th>
                <th style="width:60px">操作</th>
            </tr>
<{if empty($info)}>
            <tr>
                <td colspan="6">该版面没有任何模版</td>
            </tr>
<{else}>
<{foreach from=$info item=item key=k}>
            <tr>
                <td><{$k+1}></td>
                <td><{$item.type}></td>
                <td><{$item.title}></td>
                <td><{$item.start}></td>
                <td><{$item.day}></td>
                <td><a href="<{$base}>/board/<{$bName}>/vote/<{$k+1}>">我要投票</a></td>
            </tr>
<{/foreach}>
<{/if}>
            </table>
        </div>
        </div>
