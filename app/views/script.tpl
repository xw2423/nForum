<!-- Javascript -->
<{if !empty($jsr)}>
<script type="text/javascript">
<{foreach from=$jsr item=jsr}>
<{$jsr}>
<{/foreach}>
</script>
<{/if}>
<{if !empty($js)}>
<{foreach from=$js item=js}>
<script type="text/javascript" src="<{$static}><{$base}>/<{$js}>"></script>
<{/foreach}>
<{/if}>
<{if !empty($js_out)}>
<script type="text/javascript">
<{$js_out}>
</script>
<{/if}>
