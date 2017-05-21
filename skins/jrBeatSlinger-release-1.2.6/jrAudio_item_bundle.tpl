{if isset($bundle_item)}

    {jrCore_module_url module="jrAudio" assign="murl"}

    <div class="list_item">
        <div class="wrap clearfix">
            <div class="col4">
                <div class="image">
                    <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module="jrAudio"
                        type="audio_image"
                        item_id=$bundle_item._item_id
                        size="xlarge"
                        crop="auto"
                        class="iloutline img_scale"
                        alt=$bundle_item.audio_title
                        width=false
                        height=false}</a>
                </div>
            </div>
            <div class="col8">
                {if $bundle_item.audio_file_extension == 'mp3'}
                    {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$bundle_item}
                {/if}

                <span class="title"><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.audio_title_url}">{$bundle_item.audio_title}</a></span>
                <span class="date"><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/albums/{$bundle_item.audio_album_url}">{$bundle_item.audio_album}</a></span>
                <span class="date">{$bundle_item.audio_genre}</span>
                <span>{$bundle_item.audio_text|truncate:200}</span>
                <div class="list_buttons">
                    {jrCore_module_function function="jrFoxyCartBundle_button" module="jrAudio" field="audio_file" item=$bundle_item}
                    {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrAudio`$bundle_item._item_id`" module="jrAudio" bundle_id=$bundle_id item=$bundle_item}
                </div>
                <div class="data clearfix">
                    <span>{$bundle_item.audio_comment_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="109" default="Comments"}</span>
                    <span>{$bundle_item.audio_like_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="110" default="Likes"}</span>
                </div>
            </div>
        </div>
    </div>

{/if}
