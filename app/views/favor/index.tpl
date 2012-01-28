    	<div class="mbar corner">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件</a></li>
                <li><a href="<{$base}>/refer">文章提醒</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li class="selected"><a href="<{$base}>/fav">收藏版面</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
			<div id="fav_op">
				<input id="fav_update" type="button" class="button" value="刷新" />
				<input id="fav_up" type="button" class="button" value="上一层" />
			</div>
			<div id="fav_add">
				添加版面:&ensp;<input type="text" id="fav_ab_txt" class="input-text" /><input type="button" id="fav_ab_btn"  class="button"value="增加" />
                添加目录:&ensp;<input type="text" id="fav_ad_txt" class="input-text"/><input type="button" id="fav_ad_btn"  class="button" value="增加" />
			</div>
            <table class="board-list" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th class="title_1">版面名称</th>
                    <th class="title_2">版主</th>
                    <th class="title_3">最新主题</th>
                    <th class="title_4 middle">在线人数</th>
                    <th class="title_5 middle">今日贴数</th>
                    <th class="title_6 middle">主题数</th>
                    <th class="title_7 middle">文章数</th>
                    <th class="title_8 middle">操作</th>
            	</tr>
                </thead>
                <tbody id="fav_list"></tbody>
            </table>
    	</div>
<script id="tmpl_fav" type="text/template">
                <tr id="fav_item_<%=name%>">
                    <td class="title_1">
						<a href="<{$base}>/<%=type%>/<%=name%>"<%if(type=='fav'){%> class="fav-link"<%}%>><%=description%></a>
                    <%if(type!='fav'){%>
						<br /><%=name%>
                    <%}%>
					</td>
                    <td class="title_2">
                    <%if(type=='fav'){%>
                        [自定义目录]
                    <%}else if(type=='section'){%>
                        [二级目录]
                    <%}else{%>
						<%=manager%>
                    <%}%>
					</td>
                    <td class="title_3">
                    <%if(last.id){%>
						<a href="<{$base}>/article/<%=name%>/<%=last.id%>"><%=last.title%></a><br />  
						回复:&ensp;<a href="<{$base}>/user/query/<%=last.owner%>"><%=last.owner%></a> 日期:&ensp;<%=last.date%>
                    <%}%>&nbsp;
					</td>
                    <td class="title_4 middle"><%=user_online_count%>&nbsp;</td>
                    <td class="title_5 middle"><%=post_today_count%>&nbsp;</td>
                    <td class="title_6 middle"><%=post_threads_count%>&nbsp;</td>
                    <td class="title_7 middle"><%=post_all_count%>&nbsp;</td>
                    <td class="title_8 middle"><a href="javascript:void(0)" class="fav-del" _ac="<%if(type=='fav'){%>dd<%}else{%>db<%}%>" _npos="<%=(position == -218)?name:position%>">删除</a></td>
            	</tr>
</script>
