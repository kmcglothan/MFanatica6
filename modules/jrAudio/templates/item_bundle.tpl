{if isset($bundle_item)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="col4">
        <div class="item">
            {if jrProfile_is_profile_owner($item._profile_id)}
            <div class="table">
                <div class="table-row">
                    <div class="table-cell" style="text-align: right">
                        {if jrCore_module_is_active('jrFoxyCartBundle')}
                            {jrCore_module_function function="jrFoxyCartBundle_button" module="jrAudio" field="audio_file" item=$bundle_item}
                            {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrAudio`$bundle_item._item_id`" module="jrAudio" bundle_id=$bundle_id item=$bundle_item}
                        {elseif jrCore_module_is_active('jrBundle')}
                            {jrCore_lang module="jrBundle" id=31 default="remove from bundle" assign="dlt"}
                            {jrCore_lang module="jrBundle" id=32 default="Are you sure you want to remove this item from this bundle?" assign="dlp"}
                            <a title="{$dlt}" onclick="jrCore_confirm('{$dlt|addslashes}', '{$dlp|addslashes}', function() { jrBundle_remove({$item._item_id}, '{$bundle_item.bundle_module}', '{$bundle_item._item_id}'); } )">{jrCore_icon icon="close" size=20}</a>
                        {/if}
                    </div>
                </div>
            </div>
            {/if}
            <div class="bundle-image">
                {if $bundle_item.bundle_only == 'on'}
                    {* this item is only available in this bundle *}
                    <div class="bundle_only">
                        <i>{jrCore_lang module="jrBundle" id=39 default="Available only as part of this bundle!"}</i>
                    </div>
                {/if}
                <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$bundle_item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$bundle_item.audio_title width=false height=false}</a>
                {if $bundle_item.audio_file_extension == 'mp3'}
                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$bundle_item}<br>
                {/if}
            </div>
            <div class="bundle-item-info">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell">
                            <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">{$bundle_item.audio_title}</a></h3>
                            <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$bundle_item.audio_genre}</span><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/albums/{$bundle_item.audio_album_url}">{$bundle_item.audio_album}</a></span>
                        </div>
                        <div class="table-cell">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$bundle_item._item_id current=$bundle_item.audio_rating_1_average_count|default:0 votes=$bundle_item.audio_rating_1_number|default:0 }
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
