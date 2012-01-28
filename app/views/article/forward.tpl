<script id="tmpl_forward" type="text/template">
<form id="a_forward" action="<%=action%>" method="post">
	<ul>
	<li><span>收件人:</span><input type="text" class="input-text" name="target"/>
        <select id="a_forward_list">
            <option value="">选择好友</option>
        <%_.each(friends,function(f){%>
            <option value="<%=f%>"><%=f%></option>
        <%});%>
        </select>
    </li>
	<li><span>合集转寄:</span><input type="checkbox" name="threads" />
    &emsp;&emsp;<span>合集无引文:</span><input type="checkbox" name="noref" />
    </li>
	<li><span>不含附件:</span><input type="checkbox" name="noatt" /></li>
</form>
</script>
