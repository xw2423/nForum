<div id="upload">
    <div id="upload_op">
        <input id="upload_select" type="button" value="选择文件" class="submit" />
        <input id="upload_upload" type="button" value="上传文件" class="submit" />
        <span id="upload_info">
            个数限制:<span class="upload-max-num"><{$maxNum}></span>
            大小限制:<span class="upload-max-size"><{$maxSize}></span>
            <span class="upload-msg"></span>
        </span>
    </div>
    <table id="upload_result">
        <thead>
            <tr>
                <th style="width:40px;">序号</td>
                <th>文件名</td>
                <th style="width:60px;">大小</td>
                <th style="width:60px;">操作</td>
            </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="4">共<span style="color:red" id="upload_num_count"></span>个文件，总大小<span style="color:red" id="upload_size_count"></span></td>
            </tr>
        </tfoot>
    </table>
</div>
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
