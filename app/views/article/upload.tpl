<script id="tmpl_upload_file" type="text/template">
<td><%=no%>.</td>
<td><%-name%>
<%if(status===plupload.QUEUED){%>
<span style="color:blue">(准备上传)</span>
<%}else if(status===plupload.UPLOADING){%>
<span style="color:blue">(<%=percent%>%)</span>
<%}else if(status===plupload.DONE){%>
<span style="color:green">(上传成功)</span>
<%}else if(status===plupload.FAILED){%>
<span style="color:red">(上传失败:<%=err%>)</span>
<%}%>
</td>
<td><%=$.sizeFormat(size)%></td>
<td><a href="javascript:void(0)">
<%if(status===plupload.DONE || status===plupload.XWNONE ){%>
删除
<%}else{%>
<span style="color:blue">取消</span>
<%}%></a></td>
</script>
