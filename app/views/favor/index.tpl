<{include file="header.tpl"}>
    	<div class="mbar corner">
        	<ul>
                <li><a href="<{$base}>/user/info">基本资料修改</a></li>
                <li><a href="<{$base}>/user/passwd">昵称密码修改</a></li>
                <li><a href="<{$base}>/user/custom">用户自定义参数</a></li>
                <li><a href="<{$base}>/mail">用户信件服务</a></li>
                <li><a href="<{$base}>/friend">好友列表</a></li>
                <li class="selected"><a href="<{$base}>/fav">收藏版面管理</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
			<div id="f_op">
				<input id="update" type="button" class="button" value="刷新" />
				<input id="pLevel" type="button" class="button" value="上一层" />
			</div>
			<div id="f_add">
				添加版面:&nbsp;<input type="text" id="ab_txt"  class="input-text" /><input type="button" id="board_btn"  class="button"value="增加" />&nbsp;&nbsp;&nbsp;&nbsp;添加目录:&nbsp;<input type="text" id="ad_txt" class="input-text"/><input type="button" id="dir_btn"  class="button" value="增加" />
			</div>
			<table class="board-title" cellpadding="0" cellspacing="0">
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
            </table>
            <table class="board-list" cellpadding="0" cellspacing="0">
				<tbody id="ajaxArea" style="display:none">
                <tr>
                    <td class="title_1">
						<a href="<{$base}>/board/%0%">%1%</a>
						<br />%0%
					</td>
                    <td class="title_2">
						%2%
					</td>
                    <td class="title_3">
						<a href="<{$base}>/article/%0%/%3%">%4%</a><br />  
						回复:&nbsp;%5% 日期:&nbsp;%6%
					</td>
                    <td class="title_4 middle">%7%</td>
                    <td class="title_5 middle">%8%</td>
                    <td class="title_6 middle">%9%</td>
                    <td class="title_7 middle">%10%</td>
                    <td class="title_8 middle"><a href="%11%">删除</a></td>
            	</tr>
				</tbody>
            </table>
    	</div>
<{include file="footer.tpl"}>
