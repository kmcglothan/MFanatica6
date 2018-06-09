{jrCore_module_url module="jrSoundCloud" assign="murl"}
{if isset($_items)}

    {foreach from=$_items item="item"}
    <div class="item">
        <div class="container">
            <div class="row">
                <div class="col4">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}"><img src="{$item.soundcloud_artwork_url}" alt="{$item.soundcloud_title|jrCore_entity_string}" /></a>
                    </div>
                </div>
                <div class="col2">
                    <div class="p5">
                        {jrSoundCloud_player params=$item}
                    </div>
                </div>
                <div class="col6">
                    <div class="p5" style="overflow-wrap:break-word">
                        <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.soundcloud_title_url}">{$item.soundcloud_title|truncate:40}</a></span>
                        <span class="info">{jrCore_lang module="jrSoundCloud" id="26" default="artist"}:</span> <span class="info_c">{$item.soundcloud_artist}</span><br>
                        {if strlen($item.soundcloud_genre)> 0}
                            <span class="info">{jrCore_lang module="jrSoundCloud" id="27" default="genre"}:</span> <span class="info_c">{$item.soundcloud_genre}</span>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}