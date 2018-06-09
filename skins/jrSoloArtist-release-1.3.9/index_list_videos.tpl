{if isset($_items)}
    {jrCore_module_url module="jrVideo" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="p5 center top">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.video_title title=$item.video_title class="iloutline"}</a>
        </div>
    {/foreach}
{/if}