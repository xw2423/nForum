<div class="view-wrap">
    <form action="<{$base}>/vote/ajax_vote/<{$vinfo.vid}>.json" method="post" id="f_view">
        <h1><{$vinfo.title}><span>(<{if $vinfo.type=="0"}>单选<{else}><{if $vinfo.limit==0}>无限制<{else}>可选<{$vinfo.limit}><{/if}><{/if}>)</span></h1>
        <h2>发起时间:<{$vinfo.start}>&nbsp;&nbsp;&nbsp;截止日期:<{$vinfo.end}><{if $vinfo.isEnd}><font color="red">(已截止)</font><{/if}><{if $vinfo.isDel}><font color="red">(已删除)</font><{/if}>&nbsp;&nbsp;&nbsp;结果显示:<{if $result_voted}>需投票<{else}>无需投票<{/if}>&nbsp;&nbsp;&nbsp;参与人数:<{$vinfo.num}></h2>
<{if $vinfo.desc!=""}>
        <h3><{$vinfo.desc}></h3>
<{/if}>
        <table id="vote_table" cellpadding="0" cellspacing="0" _limit="<{$vinfo.limit}>">
<{foreach from=$vitems item=item}>
            <tr>
                <td class="col1"><{$item.label}>:</td>
                <td class="col2"><div class="vote-scroll corner"><span class="corner" style="width:0" _width="<{if $no_result}>0<{else}><{$item.percent}><{/if}>"></span></div></td>
                <td class="col3"><{if $no_result}>投票看结果<{else}><{$item.num}>(<{$item.percent}>%)<{/if}></td>
<{if $vinfo.type=="0"}>
                <td class="col4"><input type="radio" name="v<{$vinfo.vid}>" value="<{$item.viid}>"<{if $vinfo.voted || $vinfo.isEnd || $vinfo.isDel}> disabled="true"<{if $item.on}> checked="true"<{/if}><{/if}> /></td>
<{else}>
                <td class="col4"><input type="checkbox" name="v<{$vinfo.vid}>_<{$item.viid}>"<{if $vinfo.voted || $vinfo.isEnd || $vinfo.isDel}> disabled="true"<{if $item.on}> checked<{/if}><{/if}> /></td>
<{/if}>
                <td class="col5">&nbsp;</td>
            </tr>
<{/foreach}>
        </table>
        <div class="vote-submit">
<{if !$islogin}>
请登录后进行投票
<{elseif !$vinfo.voted}>
<{if !$vinfo.isDel&& !$vinfo.isEnd}>
            <input type="submit" class="button" value="提交" />
            <input type="reset" class="button" value="重置" />
<{/if}>
<{else}>
你在 <{$myres.time}> 参与了此投票。
<{/if}>
        </div>
    </form>
</div>
