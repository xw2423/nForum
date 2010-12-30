<{include file="script.tpl"}>
<style>
*{ margin:0px; padding:0px; }
a { color:#6595D6; text-decoration:none; }
a:hover { color:#6595D6; text-decoration:underline; }
body{ font-family:Arial,Verdana,Sans-Serif; font-size:0.8em; }
.submit{ border:1px solid #77a2d2; background:#ecf4fd; -moz-border-radius: 5px;  -webkit-border-radius: 5px; padding:0; margin:0; color:#4c6785; }
</style>
<{if $upload}>
<form method="post" ENCTYPE="multipart/form-data" action="<{$base}>/user/face"> 
<input type="file" name="myface" size="20" />
<input type="submit" class="submit" value="上传" /> &nbsp;&nbsp;&nbsp;&nbsp;
</form>
<{else}>
<{$msg}>,&nbsp;<a href="javascript:window.location.href='<{$base}>/user/face'" >点击此重新上传</a>
<{if isset($img)}>
<script type="text/javascript">
	var img = parent.document.getElementById("fpreview"),
	 url = parent.document.getElementById("furl"),
	 fw = parent.document.getElementById("fwidth"),
	 fh = parent.document.getElementById("fheight"),
	 w = <{$width}>,
	 h = <{$height}>,
	 mw = 120,
	 mh = 120,
	 base = parent.config.base;
	if(w >= h && w > mw){
		h = Math.ceil(h * mw / w);
		w = mw;
	}else if(h >= w && h > mh){
		w = Math.ceil(w * mh / h);
		h = mh;
	}
	img.src = base + "/<{$img}>";
	fw.value = img.width = w;
	fh.value = img.height = h;
	url.value = "<{$img}>";
</script>
<{/if}>
<{/if}>
