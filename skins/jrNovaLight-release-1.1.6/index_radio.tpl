{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="item">
        <div class="inner leader">
            {if jrUser_is_master() || jrUser_is_admin()}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{jrCore_icon icon="gear" size="24"}</a>&nbsp;
            {/if}
            <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="35" default="community"} {jrCore_lang  skin=$_conf.jrCore_active_skin id="43" default="radio"}</span>
        </div>

        <div class="container">
            <div class="row">
                <div class="col3">
                    <div class="block_image center middle">
                        {if jrCore_is_mobile_device()}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="large" crop="portrait" alt=$item.playlist_title title=$item.playlist_title class="iloutline img_scale" style="max-width:256px;max-height:256px;"}</a>
                        {else}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="small" crop="auto" alt=$item.playlist_title title=$item.playlist_title class="iloutline img_scale" style="max-width:72px;max-height:72px;"}</a>
                        {/if}
                    </div>
                </div>
                <div class="col9 last">
                    <div class="p10" style="display: table;margin-left: 10px;">
                        <div style="display: table-row;">
                            <div class="left top" style="display: table-cell;">
                                <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{$item.playlist_title}</a></h3><br>
                                <span class="media_title">{jrCore_lang module="jrPlaylist" id="10" default="Tracks"}:</span> <span class="normal">{$item.playlist_count}</span><br>
                                <div style="padding-top:4px;">
                                    {jrCore_module_function function="jrRating_form" type="star" module="jrPlaylist" index="1" item_id=$item._item_id current=$item.playlist_rating_1_average_count|default:0 votes=$item.playlist_rating_1_count|default:0 }
                                    <div class="clear"></div>
                                </div>
                            </div>
                            <div class="left middle" style="display: table-cell;padding-left: 15px;">
                                <a onclick="popwin('{$jamroom_url}/radio_player/playlist_id={$item._item_id}/title={$item.playlist_title}/autoplay=true','radio_player',820,725,true);">{jrCore_image image="button_player_play.png" width="32" height="32" alt="{$item.playlist_title}" title="{$item.playlist_title}" onmouseover="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play_hover.png');" onmouseout="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play.png');"}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    {/foreach}
{/if}
