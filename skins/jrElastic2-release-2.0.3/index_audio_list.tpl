{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        <div class="table audio">
            <div class="table-row">
                <div class="table-cell" style="width:34px; text-align: right;">
                    <h1>{$item.list_rank}</h1>
                </div>
                <div class="table-cell" style="width:50px;">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{jrCore_module_function function="jrImage_display" module='jrAudio' type='audio_image' item_id=$item._item_id size="large" crop="auto" class="img_scale" alt=$item.audio_title width=false height=false}</a>
                </div>
                <div class="table-cell">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">{$item.audio_title}</a>
                    <br>
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a>
                </div>
                {if !jrCore_is_mobile_device()}
                    {if jrCore_module_is_active('jrLike')}
                        <div class="table-cell desk" style="width:40px;">
                            {jrLike_button item=$item module="jrAudio" action="like"}
                        </div>
                    {/if}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="table-cell desk" style="width: 120px; text-align: center">
                            <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                                function="jrRating_form"
                                type="star"
                                module="jrAudio"
                                index="1"
                                item_id=$item._item_id
                                current=$item.audio_rating_1_average_count|default:0
                                votes=$item.audio_rating_1_number|default:0}
                            </div>
                        </div>
                    {/if}

                {/if}
                <div class="table-cell" style="width: 40px; text-align: right">
                    {if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                        {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                    {else}
                        &nbsp;
                    {/if}
                </div>
            </div>
        </div>
    {/foreach}
{/if}