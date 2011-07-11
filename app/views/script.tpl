<!-- Javascript -->
<{if !empty($jsr)}>
<script type="text/javascript">
<{foreach from=$jsr item=item}>
<{$item}>
<{/foreach}>
</script>
<{/if}>
<{if !empty($js)}>
<{foreach from=$js item=item}>
<script type="text/javascript" src="<{$static}><{$base}>/<{$item}>"></script>
<{/foreach}>
<{/if}>
<{if !empty($js_out)}>
<script type="text/javascript">
<{$js_out}>
</script>
<{/if}>
