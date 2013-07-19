    <div class="t-pre">
        <div class="bmode">
            <span>版面模式:</span>
            <a class="tab-normal tab-down" href="<{$base}>/board/<{$bName}>">主题</a>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/6">原作</a>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/0">经典</a>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/3">保留</a>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/1">文摘</a>
<{if $isAdmin || $bm}>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/4">回收</a>
<{/if}>
<{if $isAdmin}>
            <a class="tab-normal" href="<{$base}>/board/<{$bName}>/mode/5">纸篓</a>
<{/if}>
        </div>
		<div class="t-btn">
<{if !$tmpl}>
			<a href="<{$base}>/article/<{$bName}>/post" class="b-post button">新话题</a>
<{/if}>
			<a href="<{$base}>/article/<{$bName}>/tmpl" class="b-post button">模版发文</a>
<{if $hasVote}>
			<a href="<{$base}>/board/<{$bName}>/vote" class="button">版内投票</a>
<{/if}>
			<a href="<{$base}>/elite/path?v=<{$elitePath}>" class="button">精华区</a>
			<{if $islogin}><a href="javascript:void(0)" id="b_fav" class="button" _b="<{$bName}>">收藏</a><{/if}>
			<{if $bm || $isAdmin}><a href="<{$base}>/board/<{$bName}>/denylist" class="button">封禁列表</a><{/if}>
			<a href="<{$base}>/rss/board-<{$bName}>" class="rss" target="_blank"><samp class="ico-pos-rss"></samp></a>
		</div>
    </div>
	<{capture name=n_left}>本版当前共有<{$curNum}>人在线<{if isset($maxNum)}><span title="发生于<{$maxTime}>">[最高<{$maxNum}>人]</span><{/if}>&emsp;今日帖数<{$todayNum}><{/capture}>
	<{capture name=n_right}>
	版主:
		<{foreach from=$bms item=item}>
			&ensp;<{if $item[1]}><a href="<{$base}>/user/query/<{$item[0]}>"><{$item[0]}></a><{else}><{$item[0]}><{/if}>
		<{/foreach}>
	<{/capture}>

	<{include file="s_nav.tpl" nav_left=$smarty.capture.n_left nav_right=$smarty.capture.n_right}>
        <div class="b-content">
            <table class="board-list tiz" cellpadding="0" cellspacing="0">
                <thead>
                <tr>
                    <th class="title_8">状态</th>
                    <th class="title_9 middle">主题</th>
                    <th class="title_10">发帖时间</th>
                    <th class="title_12">|&ensp;作者</th>
                    <th class="title_11 middle">回复</th>
                    <th class="title_10">最新回复</th>
                    <th class="title_12">|&ensp;作者</th>
            	</tr>
                </thead>
                <tbody>
<{if ($info)}>
<{foreach from=$info item=item}>
				<tr <{if $item.tag == "T"}>class="top"<{/if}>>
					<td class="title_8">
                    <{if $bm || $isAdmin}>
                    <a class="a-func-manage" href="<{$base}>/article/<{$bName}>/ajax_manage/<{$item.gid}>.json" title="管理" _gid="<{$item.gid}>">
                    <{else}>
					<a target="_blank" href="<{$base}>/article/<{$bName}>/<{$item.gid}>" title="在新窗口打开此主题">
                    <{/if}>
                    <samp class="tag
					<{if $item.tag == "T"}> ico-pos-article-top
                    <{elseif $item.tag == "B"}> ico-pos-article-b
					<{elseif $item.tag == "M"}> ico-pos-article-m
					<{elseif $item.tag == "G"}> ico-pos-article-g
					<{elseif $item.tag == ";"}> ico-pos-article-lock
					<{elseif $item.tag == "L"}> ico-pos-article-light
                    <{elseif $item.tag == "L2"}> ico-pos-article-fire
                    <{elseif $item.tag == "L3"}> ico-pos-article-huo
					<{else}> ico-pos-article-normal
					<{/if}>"></samp></a></td>
					<td class="title_9">
						<a href="<{$base}>/article/<{$bName}>/<{$item.gid}>"><{$item.title}></a>
                    <{if $item.att}><samp class="tag-att ico-pos-article-attach"></samp><{/if}>
		<{if $item.page>7}>
		<span class="threads-tab">[<{section name=temp loop=7 start=2}><a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$smarty.section.temp.index}>"><{$smarty.section.temp.index}></a><{/section}>..<a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$item.page}>"><{$item.page}></a>]</span>
		<{elseif $item.page>1}>
		<span class="threads-tab">[<{section name=temp loop=$item.page+1 start=2}><a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$smarty.section.temp.index}>"><{$smarty.section.temp.index}></a><{/section}>]</span>
		<{/if}>
					</td>
                    <td class="title_10"><{$item.postTime}></td>
                    <td class="title_12">|&ensp;<a href="<{$base}>/user/query/<{$item.poster}>" class="c63f"><{$item.poster}></a></td>
                    <td class="title_11 middle"><{$item.num}></td>
                    <td class="title_10"><a href="<{$base}>/article/<{$bName}>/<{$item.gid}>?p=<{$item.page}>#a<{$item.num}>" title="跳转至最后回复"><{$item.replyTime}></a></td>
                    <td class="title_12">|&ensp;<a href="<{$base}>/user/query/<{$item.last}>" class="c09f"><{$item.last}></a></td>
				</tr>
<{/foreach}>
<{else}>
				<tr>
					<td colspan="7" style="text-align:center">该版面没有任何主题</td>
				</tr>
<{/if}>
                </tbody>
			</table>
    	</div>
    <div class="t-pre-bottom">
        <div class="page">
            <{include file="pagination.tpl" page_name='主题数'}>
        </div>
    	<div class="t-btn">
        	<form method="get" action="<{$base}>/s/article" id="board_search">
        		<input type="text" class="input-text input" name="t1" placeholder="输入关键字" size="16"/>
        		<input type="text" class="input-text input" name="au" placeholder="输入作者ID" size="12"/>
                <input type="checkbox" name="m" id="c_m"/>
                <label for="c_m">精华帖</label>
                <input type="checkbox" name="a" id="c_a"/>
                <label for="c_a">带附件</label>
                <input type="submit" class="button" value="搜索" />
				<input type="hidden" name="b" value="<{$bName}>" />
            </form>
        </div>
    </div>
<{if $bm || $isAdmin}><{include file="article/manage.tpl"}><{/if}>
