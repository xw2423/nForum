<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li <{if $type=="section"}>class="selected"<{/if}>><a href="<{$base}>/widget/add?t=section">讨论区</a></li>
                <li <{if $type=="board"}>class="selected"<{/if}>><a href="<{$base}>/widget/add?t=board">版面</a></li>
                <li <{if $type=="favor"}>class="selected"<{/if}>><a href="<{$base}>/widget/add?t=favor">收藏夹</a></li>
                <li <{if $type=="ext"}>class="selected"<{/if}>><a href="<{$base}>/widget/add?t=ext">其他应用</a></li>
            </ul>					
        </div>
        <div class="b-content corner">
<{if isset($filter)}>
        	<ul class="w-filter">
<{foreach from=$filter item=item key=k}>
				<li class="tab-normal" id="filter_<{$k}>"><{$item}></li>
<{/foreach}>
			</ul>
<{/if}>
        	<ul id="application" style="display:none" class="corner">
            	<li id="%0%" title="%2%">
                	<h5 class="app-title">%1%</h5>
                    <div class="app-logo"><img _src="<{$base}>/img/app/icon/%3%.png" /></div>
                    <div class="app-submit"><input type="submit" class="submit" value="添加到首页" /></div>
                </li>
            </ul>
<{if isset($search)}>
            <div class="w-search"><span>搜索应用:&nbsp;</span><input class="input-text" type="text" id="w_search_txt" value="输入应用名称" /><input class="button" type="button" id="w_search_btn" value="搜  索" /></div>
<{/if}>
    	</div>
	<div id="addform" style="display:none">
			<ul>
        	<li><span>标题:&nbsp;</span><input type="text" id="title" size="20" class="input-text"/></li>
            <li><span>颜色:&nbsp;</span><select id="color">
					<{html_options options=$colors selected=$color}>
				</select>
			</li>
        	<li>
				<span>列:&nbsp;&nbsp;&nbsp;&nbsp;</span><select id="col">
					<option value="1">1</option>
					<option value="2">2</option>
					<{if $line3}>
					<option value="3">3</option>
					<{/if}>
				</select>
			</li>
            <li><input type="submit" class="submit" value="添加应用"/></li>
			</ul>
			<input type="hidden" id="wid" value="" />
    </div>  
<{include file="footer.tpl"}>
