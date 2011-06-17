<{include file="header.tpl"}>
    	<div class="mbar">
        	<ul>
                <li><a href="<{$base}>/vote?c=new">最新投票</a></li>
                <li><a href="<{$base}>/vote?c=hot">热门投票</a></li>
                <li><a href="<{$base}>/vote?c=all">全部投票</a></li>
<{if $islogin}>
                <li><a href="<{$base}>/vote?c=list&u=<{$id}>">我的投票</a></li>
                <li><a href="<{$base}>/vote?c=join">我参与的投票</a></li>
                <li><a href="<{$base}>/vote/add">新投票</a></li>
<{/if}>
<{if $isAdmin}>
                <li><a href="<{$base}>/vote?c=del">已删除的投票</a></li>
<{/if}>
            </ul>					
        </div>
		<div class="b-content vote-main">
			<div class="vote-title">查看投票</div>
			<div id="vote_view" class="vote-left">
				<div class="view-wrap">
				<form action="" method="post">
				<h1><{$info.title}><span>(<{$info.limitMsg}>)</span></h1>
				<h2>发起时间:<{$info.start}>&nbsp;&nbsp;&nbsp;截止日期:<{$info.end}><{if $info.isEnd}><font color="red">(已截止)</font><{/if}><{if $info.isDel}><font color="red">(已删除)</font><{/if}>&nbsp;&nbsp;&nbsp;参与人数:<{$info.num}></h2>
<{if $info.desc!=""}>
				<h3><{$info.desc}></h3>
<{/if}>
				<table id="vote_table" cellpadding="0" cellspacing="0" _limit="<{$info.limit}>">
<{foreach from=$item item=item}>
					<tr>
						<td class="col1"><{$item.label}>:</td>
						<td class="col2"><div class="vote-scroll corner"><span class="corner" style="width:<{$item.percent}>%"></span></div></td>
						<td class="col3"><{$item.num}>(<{$item.percent}>%)</td>
<{if $info.type=="0"}>
						<td class="col4"><input type="radio" name="v<{$info.vid}>" value="<{$item.viid}>"<{if $info.voted || $info.isEnd || $info.isDel}> disabled="true"<{if $item.on}> checked="true"<{/if}><{/if}> /></td>
<{else}>
						<td class="col4"><input type="checkbox" name="v<{$info.vid}>_<{$item.viid}>"<{if $info.voted || $info.isEnd || $info.isDel}> disabled="true"<{if $item.on}> checked<{/if}><{/if}> /></td>
<{/if}>
					</tr>
<{/foreach}>
				</table>
				<div class="vote-submit">
<{if !$islogin}>
请登录后进行投票
<{elseif !$info.voted}>
<{if !$info.isDel&& !$info.isEnd}>
					<input type="submit" class="button" value="提交" />
					<input type="reset" class="button" value="重置" />
<{/if}>
<{else}>
你在 <{$myres.time}> 参与了此投票。
<{/if}>
				</div>
				</form>
				</div>
			</div>
			<div id="vote_info" class="vote-right">
<{if !$info.isDel}>
				<li class="widget color-white">  
					<div class="widget-head">
						<span class="widget-title vote-hot">操作列表</span>	
					</div>
					<div class="widget-content">
						<ul class="w-list-line">
						<li><a href="<{$base}>/article/<{$board}>/<{$info.aid}>">查看评论</a></li>
<{if $islogin}>
						<li><a href="<{$base}>/article/<{$board}>/post?reid=<{$info.aid}>">我要评论</a></li>
<{/if}>
<{if $admin}>
						<li><a href="<{$base}>/vote/delete/<{$info.vid}>" onclick="return confirm('确认删除此投票?')">删除此投票</a></li>
<{/if}>
						</ul>
					</div>
				</li>
<{/if}>
				<li class="widget color-default">  
					<div class="widget-head">
						<span class="widget-title vote-user">发起人</span>	
					</div>
					<div class="widget-content">
						<div class="w-free">
<{if $furl}>
						<img src="<{$base}><{$furl}>" <{if $fwidth != ""}>width="<{$fwidth}>px"<{/if}> <{if $fheight != ""}>height="<{$fheight}>px"<{/if}> />
<{/if}>
						<span>ID:<{$info.uid}></span>

						</div>
						<ul class="w-list-line">
						<li><a href="<{$base}>/vote?c=list&u=<{$info.uid}>">查看他的投票</a></li>
						<li><a href="<{$base}>/user/query/<{$info.uid}>">查看他的信息</a></li>
<{if $islogin}>
						<li><a href="<{$base}>/mail/send?id=<{$info.uid}>">给他发站内信</a></li>
						<li><a href="<{$base}>/friend/add?id=<{$info.uid}>">加他为好友</a></li>
<{/if}>
						</ul>
					</div>
				</li>
			</div>
		</div>
<{include file="footer.tpl"}>
