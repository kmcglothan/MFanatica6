{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col7">
                    <div class="p5">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{$item.playlist_title}</a></h2><br>
                        <span class="info">{jrCore_lang module="jrPlaylist" id="10" default="Tracks"}:</span> <span class="info_c">{$item.playlist_count}</span><br>
                    </div>
                </div>
                <div class="col3">
                    <div class="p5">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrPlaylist" index="1" item_id=$item._item_id current=$item.playlist_rating_1_average_count|default:0 votes=$item.playlist_rating_1_count|default:0}
                    </div>
                </div>
                <div class="col2 last">
                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrPlaylist" item=$item}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>

        </div>

    </div>
    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
