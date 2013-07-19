    	<div class="mbar">
        	<ul>
                <li <{if $category=="new"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=new">最新投票</a></li>
                <li <{if $category=="hot"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=hot">热门投票</a></li>
                <li <{if $category=="all"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=all">全部投票</a></li>
<{if $islogin}>
                <li <{if $category=="list"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=list&u=<{$id}>">我的投票</a></li>
                <li <{if $category=="join"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=join">我参与的投票</a></li>
                <li><a href="<{$base}>/vote/add">新投票</a></li>
<{/if}>
<{if $isAdmin}>
                <li <{if $category=="del"}>class="selected"<{/if}>><a href="<{$base}>/vote?c=del">已删除的投票</a></li>
<{/if}>
            </ul>					
        </div>
		<div class="b-content vote-main">
			<div class="vote-title"><{$voteTitle}></div>
			<div id="vote_rank" class="vote-right">
				<div class="vote-search">
                    <form action="<{$base}>/vote" method="get">
                        <input type="text" class="input-text" placeholder="标题,发起人,描述关键字" name="s" value="<{$search|default:''}>"/>&ensp;<input type="submit" class="button" value="筛选"/>
                        <input type="hidden" name="c" value="<{$category}>"/>
<{if isset($vote_user)}>
                        <input type="hidden" name="u" value="<{$vote_user}>"/>
<{/if}>
                    </form>
				</div>
				<li class="widget color-red">  
					<div class="widget-head">
						<span class="widget-title vote-hot">一周热门投票排行榜</span>	
					</div>
					<div class="widget-content">
						<ul class="w-list-line">
<{if empty($week)}>
						<li>不存在任何投票</li>
<{else}>
<{foreach from=$week item=item}>
						<li title="<{$item.subject}>"><a href="<{$base}>/vote/view/<{$item.vid}>"><{$item.subject}>(<span><{$item.num}></span>)</a></li>
<{/foreach}>
<{/if}>
						</ul>
					</div>
				</li>
				<li class="widget color-orange">  
					<div class="widget-head">
						<span class="widget-title vote-hot">本月热门投票排行榜</span>	
					</div>
					<div class="widget-content">
						<ul class="w-list-line">
<{if empty($month)}>
						<li>不存在任何投票</li>
<{else}>
<{foreach from=$month item=item}>
						<li title="<{$item.subject}>"><a href="<{$base}>/vote/view/<{$item.vid}>"><{$item.subject}>(<span><{$item.num}>)</span></a></li>
<{/foreach}>
<{/if}>
						</ul>
					</div>
				</li>
				<li class="widget color-white">  
					<div class="widget-head">
						<span class="widget-title vote-hot">年度热门投票排行榜</span>	
					</div>
					<div class="widget-content">
						<ul class="w-list-line">
<{if empty($year)}>
						<li>不存在任何投票</li>
<{else}>
<{foreach from=$year item=item}>
						<li title="<{$item.subject}>"><a href="<{$base}>/vote/view/<{$item.vid}>"><{$item.subject}>(<span><{$item.num}></span>)</a></li>
<{/foreach}>
<{/if}>
						</ul>
					</div>
				</li>
			</div>
			<div id="vote_list" class="vote-left">
<{if $info}>
				<ul class="list-main">
<{foreach from=$info item=item}>
					<li>
						<div class="c-con">
							<h1><a href="<{$base}>/vote/view/<{$item.vid}>"><{$item.title}></a><{if $item.admin && !$item.isDel}>&nbsp;&nbsp;<a href="<{$base}>/vote/ajax_delete/<{$item.vid}>.json" class="vote-delete"><span>删除</span><{/if}></a></h1>
							<h2>发起时间:<{$item.start}>&nbsp;&nbsp;截止日期:<{$item.end}><{if $item.isEnd}><font color="red">(已截止)</font><{/if}><{if $item.isDel}><font color="red">(已删除)</font><{/if}></h2>
							<h3><font color="#666">发起人:</font><a href="<{$base}>/user/query/<{$item.uid}>"><{$item.uid}></a><span><a href="<{$base}>/vote?c=list&u=<{$item.uid}>">他的投票</a></span><span><a href="<{$base}>/mail/send?id=<{$item.uid}>">站内信</a></span></h3>
						</div>
						<div class="c-num"><div><{$item.num}><br /><span>人参与</span></div></div>
					</li>
<{/foreach}>
				</ul>
<{else}>
                <div style="text-align:center;padding-top:20px;">尚无投票记录！</div>
<{/if}>
                <div class="page">
                    <ul class="pagination">
                      <li class="page-pre">投票总数:<i><{$totalNum}></i>&nbsp;&nbsp;分页:</li>
                      <li><ol title="分页列表" class="page-main"><{$pageBar}></ol></li>
                      <li class="page-suf"></li>	
                    </ul>
                </div>
			</div>
		</div>
