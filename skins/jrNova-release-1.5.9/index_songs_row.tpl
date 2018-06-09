{if isset($_items)}
<div class="container">
    {foreach from=$_items item="item"}
    {if $item@first || ($item@iteration % 4) == 1}
    <div class="row">
    {/if}
        <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
            <div class="p5" style="text-align:center;">
                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="medium" crop="auto" alt=$item.audio_title title=$item.audio_title class="iloutline img_shadow"}</a><br>
                <div style="width:196px;margin:0 auto;">
                    <table>
                        <tr>
                            <td class="media_title capital" style="text-align:center;">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$_params.module_url}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}">{$item.audio_title|truncate:20:"...":false}</a><br>
                                <span class="normal"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name}">{$item.profile_name|truncate:20:"...":false}</a></span>
                            </td>
                            <td style="text-align:right;">
                                {if $item.audio_file_extension == 'mp3'}
                                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item image="small_button"}
                                {/if}
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    {if $item@last || ($item@iteration % 4) == 0}
    </div>
    {/if}
    {/foreach}
</div>
{/if}
