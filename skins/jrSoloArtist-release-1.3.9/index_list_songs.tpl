{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="p5 center top">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline"}</a>
        </div>
    {/foreach}
{/if}