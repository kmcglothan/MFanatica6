{jrCore_module_url module="jrSoundCloud" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="list_item">
            <div class="wrap clearfix">
                <div class="col4">
                    <div class="image">
                        {if isset($item.soundcloud_artwork_url) && strlen($item.soundcloud_artwork_url) > 0}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url}" alt="{$item.soundcloud_title_url|jrCore_entity_string}" class="iloutline img_scale"></a><br>
                        {/if}
                    </div>
                </div>
                <div class="col8">
                    {jrSoundCloud_player params=$item}

                    <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title}</a></span>
                    <span class="date">{$item.soundcloud_artist}</span>
                    <span class="date">{$item.soundcloud_genre}</span>
                    <span>{$item.soundcloud_description|truncate:200}</span>
                    <div class="list_buttons">
                        {jrCore_item_list_buttons module="jrSoundCloud" item=$item}
                    </div>
                    <div class="data clearfix">
                        <span>{$item.soundcloud_comment_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="109" default="Comments"}</span>
                        <span>{$item.soundcloud_like_count|jrCore_number_format} {jrCore_lang skin="jrMogul" id="110" default="Likes"}</span>
                    </div>
                </div>
            </div>
        </div>

    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}