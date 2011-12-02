	<{include file="s_nav.tpl" nav_left="文章搜索"}>
        <div class="b-content corner">
			<form action="<{$base}>/s/article" method="get" id="search_form">
        	<div class="search">
            	<ul>
                    <li>
					<span>选择分区:</span>
					<select id="search_section">
					<{html_options options=$sec selected=$selected }>
					</select>&emsp;<span>选择版面:</span>
					<select name="b" id="search_board">
					</select>
					</li>
                    <li><span>标题含有:</span><input class="input-text input" type="text" name="t1"/><span>AND</span><input class="input-text input" type="text" name="t2"/></li>
                    <li><span>标题不含:</span><input class="input-text input" type="text" name="tn"/></li>
                    <li><span>作者账号:</span><input class="input-text input" type="text" name="au"/></li>
                    <li><span>最后回复:</span><input class="input-text input input-day" type="text" name="d" value="<{$searchDay}>"/><span>天以内</span></li>
                    <li class="search-option">
						<input class="input-check" type="checkbox" name="m" /><span>精华文章</span>
                        <input class="input-check" type="checkbox" name="a"/><span>带附件文章</span>
                        <{if $site && $isAdmin}>
                        <input class="input-check" type="checkbox" name="f"/><span>全站搜索</span>
                        <{/if}>
                    </li>
            	</ul>
				<div class="b-search">
                <input class="button" type="submit" value="查询文章" />
				</div>
        	</div>
			</form>
		</div>
