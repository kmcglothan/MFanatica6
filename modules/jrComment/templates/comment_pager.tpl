{* prev/next page profile footer links *}
{if $info.next_page > 0}
    {jrCore_module_url module="jrImage" assign="murl"}
    <div id="cpholder{$info.this_page}" class="item center p10">
        <div id="cploader" class="p10" style="display:none"><img src="{$jamroom_url}/{$murl}/img/module/jrCore/loading.gif" alt="working..."></div>
        <a onclick="jrComment_load('{$_items[0].comment_module}',{$_items[0].comment_item_id},{$info.this_page},{$info.next_page});">{jrCore_lang module="jrComment" id=17 default="Load More Comments"}</a>
    </div>
{/if}
