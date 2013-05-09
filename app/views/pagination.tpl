<ul class="pagination">
    <li class="page-pre"><{$page_name}>:<i><{$pagination->getTotalNum()}></i>&emsp;分页:</li>
    <li>
      <ol title="分页列表" class="page-main">
        <{$pageBar}>
      </ol>
    </li>
    <li class="page-jump">
        <input type="text" class="input-text" />
        <input type="button" class="button" title="跳转" value="跳" />
    </li>
</ul>
