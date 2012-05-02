<{include file="s_nav.tpl" nav_left="封禁列表"}>
<div class="b-content corner">
    <table class="board-list" id="deny_list"><thead>
    <tr>
        <th class="c_num">序号</th>
        <th class="c_userid">ID</th>
        <th class="c_reason">原因</th>
        <th class="c_desc">说明</th>
        <th class="c_op">操作</th>
    </tr></thead>
    <tr>
        <td class="c_num">0.</td>
        <td class="c_userid"><input type="text" name="id" class="input-text"/></td>
        <td class="c_reason"><input type="text" name="reason" class="input-text"/><select><option value="">选择封禁理由</option></select></td>
        <td class="c_desc"><span>封禁天数:</span><input type="text" name="day" class="input-text"/><span>(1-<{$maxday}>天)</td>
        <td class="c_op"><input id="add_deny" type="button" class="button" value="封禁" disabled="disabled" _b="<{$bName}>"/></td>
    </tr>
    <{foreach from=$data item=i key=k}>
    <tr>
        <td class="c_num"><{$k+1}>.</td>
        <td class="c_userid"><{$i.ID}></td>
        <td class="c_reason"><{$i.EXP}></td>
        <td class="c_desc"><{$i.COMMENT}><span style="display:none"><{$i.FREETIME}></span></td>
        <td class="c_op"><input class="mod_deny button" type="button" value="修改" disabled="disabled" /><input class="del_deny button" type="button" value="解封" disabled="disabled" /></td>
    </tr>
    <{/foreach}>
    </table>
</div>
<script id="tmpl_denymod" type="text/template">
<form id="m_deny" action="<%=action%>" method="post">
    <section class="list-block">
        <header>修改封禁</header>
        <ul class="narrow">
            <li>
                <span>修改封禁理由:</span><input type="text" value="<%=reason%>"class="input-text" name="reason" /><select></select>
            </li>
            <li>
                <span>当前到期时间:&nbsp;<%=freetime%></span>
            <li>
                <span>修改封禁天数:</span><input type="text" class="input-text" name="day" value="<%=day%>"/><span>(1-<%=maxday%>天, 从当前起算)</span>
            </li>
            <input type="hidden" value="<%=id%>" name="id" />
        </ul>
    </section>
</form>
</script> 
