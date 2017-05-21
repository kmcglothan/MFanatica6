{if isset($playlist_item)}

    {jrCore_module_url module="jrSoundCloud" assign="murl"}
    <div id="s{$playlist_item._item_id}" class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                    {if strlen($playlist_item.soundcloud_artwork_url) > 0}
                        <a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.soundcloud_title_url}"><a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.soundcloud_title_url}"><img src="{$playlist_item.soundcloud_artwork_url}" width="72" class="iloutline"></a>
                    {/if}
                    </div>
                </div>
                <div class="col7">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.soundcloud_title_url}">{$playlist_item.soundcloud_title}</a></h3><br>
                        <span class="info">artist:</span> <span class="info_c">{$playlist_item.soundcloud_artist}</span><br>
                        <span class="info">genre:</span> <span class="info_c">{$playlist_item.soundcloud_genre}</span>
                    </div>
                </div>
                <div class="col1">
                    <div class="p5">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrSoundCloud" index="1" item_id=$playlist_item._item_id current=$playlist_item.soundcloud_rating_1_average_count|default:0 votes=$playlist_item.soundcloud_rating_1_number|default:0}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="block_config">
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrSoundCloud" item_id=$playlist_item._item_id}
                        {jrCore_module_function function="jrPlaylist_remove_button" id="#s`$playlist_item._item_id`" module="jrSoundCloud" playlist_id=$playlist_id item_id=$playlist_item._item_id}
                    </div>
                </div>
            </div>
        </div>

    </div>

{/if}
