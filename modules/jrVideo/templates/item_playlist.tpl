{if isset($playlist_item)}

    {jrCore_module_url module="jrVideo" assign="murl"}
    <div id="v{$playlist_item._item_id}" class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$playlist_item._item_id size="small" crop="auto" class="iloutline" alt=$playlist_item.video_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col3">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/{$playlist_item._item_id}/{$playlist_item.video_title_url}">{$playlist_item.video_title}</a></h3><br>
                        <span class="info">album:</span> <span class="info_c"><a href="{$jamroom_url}/{$playlist_item.profile_url}/{$murl}/albums/{$playlist_item.video_album_url}">{$playlist_item.video_album}</a></span>
                    </div>
                </div>
                <div class="col2">
                    <div class="p5">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$playlist_item._item_id current=$playlist_item.video_rating_1_average_count|default:0 votes=$playlist_item.video_rating_1_number|default:0}
                    </div>
                </div>
                <div class="col5 last">
                    <div class="block_config">
                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrVideo" item_id=$playlist_item._item_id}
                        {jrCore_module_function function="jrPlaylist_remove_button" id="#v`$playlist_item._item_id`" module="jrVideo" playlist_id=$playlist_id item_id=$playlist_item._item_id}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

    </div>

{/if}