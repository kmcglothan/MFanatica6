{if isset($_items)}
<div class="row">
    {foreach from=$_items item="item"}
        <div class="col2{if $row@last} last{/if} center">
            <div class="center m0 p8">
                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="large" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline img_scale"}</a>
                <br>
                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}"><span class="media_title">{if strlen($item.audio_title) > 30}{$item.audio_title|truncate:30:"...":false}{else}{$item.audio_title}{/if}</span></a><br>
                <a href="{$jamroom_url}/{$item.profile_url}"><span class="normal">{$item.profile_name}</span></a><br>
            </div>
        </div>
    {/foreach}
</div>
{/if}
