{if isset($_items)}
    {foreach from=$_items item="item"}
        <div style="display:table">
            <div style="display:table-cell">
                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="small" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline"}</a>
            </div>
            <div class="p10" style="display:table-cell">
                {if $item.audio_file_extension == 'mp3'}
                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="button_player"}
                {else}
                    {jrCore_lang skin=$_conf.jrCore_active_skin id="108" default="Download" assign="alttitle"}
                    <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt=$alttitle title=$alttitle}</a>
                {/if}
            </div>
            <div class="p5" style="display:table-cell;text-align:left;vertical-align:middle">
                <h3><a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a></h3><br>
                <a href="{$jamroom_url}/{$item.profile_url}"><span class="hilite">{$item.profile_name}</span></a><br>
                <span class="normal" style="font-weight:bold;text-transform:capitalize;">{jrCore_lang skin=$_conf.jrCore_active_skin id="51" default="plays"}:</span> <span class="hilite">{$item.audio_file_stream_count}</span>
            </div>
        </div>
    {/foreach}
{/if}
