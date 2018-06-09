{if isset($_items)}
    {$rank = 0}
    {foreach from=$_items item="item"}
        {$rank = $rank+1}
        {if $rank%8 == 1}
            <div class="row">
        {/if}

        {if strlen($item.audio_title) > 0}
            {jrCore_module_url module="jrAudio" assign="murl"}
            <div class="index_item">
                <div class="wrap">
                    <div class="image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}">
                            {jrCore_module_function
                            function="jrImage_display"
                            module="jrAudio"
                            type="audio_image"
                            item_id=$item._item_id
                            size="xlarge"
                            crop="auto"
                            class="img_scale"
                            alt=$item.audio_title
                            width=false
                            height=false
                            }</a>
                    </div>
                    <span class="item_title">{$item.audio_title|truncate:20}</span>
                    <span>{jrCore_lang skin="kmSuperFans" id="34" default="by"} {$item.profile_name}</span>
                    <span>{$item.audio_album|truncate:20}</span>
                    <span>{$item.audio_genre}</span>
                    <ul class="index_buttons">
                        <li>{if $item.audio_active == 'on' && $item.audio_file_extension == 'mp3'}
                                {jrCore_media_player type="jrAudio_button" module="jrAudio" field="audio_file" item=$item}
                            {/if}
                        </li>
                        <li>
                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                        </li>
                        <li>{jrLike_button item=$item module="jrAudio" action="like" item_id=$item._item_id}</li>
                    </ul>
                </div>
            </div>
        {else}
            {jrCore_module_url module="jrSoundCloud" assign="murl"}
            <div class="index_item">
                <div class="wrap">
                    <div class="image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">
                            <img class="img_scale" src="{$item.soundcloud_artwork_url}">
                        </a>
                    </div>
                    <span class="item_title">{$item.soundcloud_title|truncate:20}</span>
                    <span>{jrCore_lang skin="kmSuperFans" id="34" default="by"} {$item.profile_name}</span>
                    <span>{$item.soundcloud_artist|truncate:20}</span>
                    <span>{$item.soundcloud_genre}</span>
                    <ul class="index_buttons">
                        <li>
                            {jrSoundCloud_player params=$item}
                        </li>

                        <li>{jrLike_button item=$item module="jrSoundCloud" action="like" item_id=$item._item_id}</li>
                    </ul>
                </div>
            </div>
        {/if}

        {if $rank%8 == 0 || $rank == $info.total_items}
            </div>
        {/if}

    {/foreach}
{else}
    <div class="no-items">
        <h1>{jrCore_lang skin="kmSuperFans" id="62" default="No items found"}</h1>
        {jrCore_module_url module="jrCore" assign="core_url"}
        {if $_conf.kmSuperFans_require_price == 'on'}
            {jrCore_lang skin="kmSuperFans" id="63" default="This list currently requires items to have a price set."}
        {/if}
        <button class="form_button" style="display: block; margin: 2em auto;" onclick="jrCore_window_location('{$jamroom_url}/{$core_url}/skin_admin/global/skin={$_conf.jrCore_active_skin}/section=List+1')">{jrCore_lang skin="kmSuperFans" id="64" default="Edit Configuration"}</button>
    </div>
{/if}