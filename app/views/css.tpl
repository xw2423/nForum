<!-- CSS -->
<{if !empty($css)}>
<{foreach from=$css item=css}>
<link rel="stylesheet" type="text/css" href="<{$static}><{$base}>/<{$css}>" />
<{/foreach}>
<{/if}>
<{if !empty($css_out)}>
<style type="text/css">
<{$css_out}>
</style>
<{/if}>
