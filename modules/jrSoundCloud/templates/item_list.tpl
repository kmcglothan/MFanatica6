{jrCore_module_url module="jrSoundCloud" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}

    <div class="item">
        <div class="container">
            <div class="row">

                <div class="col2">
                    <div class="block_image" style="position:relative">
                        {if isset($item.soundcloud_artwork_url) && strlen($item.soundcloud_artwork_url) > 0}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url|replace:"-large.jpg":"-t500x500.jpg"}" alt="{$item.soundcloud_title_url|jrCore_entity_string}" class="iloutline img_scale"></a><br>
                        {/if}
                        <div style="position:absolute;bottom:8px;right:5px">
                            {jrSoundCloud_player params=$item}
                        </div>
                    </div>
                </div>

                <div class="col7">
                    <div class="p5" style="overflow-wrap:break-word">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title}</a></h2><br>
                        <span class="info">{jrCore_lang module="jrSoundCloud" id="26" default="artist"}:</span> <span class="info_c">{$item.soundcloud_artist}</span><br>
                        {if strlen($item.soundcloud_genre)> 0}
                            <span class="info">{jrCore_lang module="jrSoundCloud" id="27" default="genre"}:</span> <span class="info_c">{$item.soundcloud_genre}</span><br>
                        {/if}
                        {jrCore_module_function function="jrRating_form" type="star" module="jrSoundCloud" index="1" item_id=$item._item_id current=$item.soundcloud_rating_1_average_count|default:0 votes=$item.soundcloud_rating_1_count|default:0 text=$text target="alert"}
                    </div>
                </div>

                <div class="col3 last">
                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrSoundCloud" item=$item}
                    </div>
                </div>

            </div>
        </div>
    </div>

    {/foreach}

{/if}