{jrCore_module_url module="jrPlaylist" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="title">
            {jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="house"} {jrCore_lang skin=$_conf.jrCore_active_skin id="43" default="radio"}
        </div>
        <div class="body_2">
            <div style="display:table">
                <div style="display:table-cell">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="small" crop="auto" alt=$item.playlist_title title=$item.playlist_title class="iloutline"}</a>
                </div>
                <div class="p5" style="display:table-cell;vertical-align:top;">
                    <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.playlist_title_url}">{$item.playlist_title}</a></h3><br>
                    <span class="normal" style="font-weight:bold;text-transform:capitalize;">{jrCore_lang module="jrPlaylist" id="10" default="Tracks"}:</span> <span class="hilite">{$item.playlist_count}</span><br>
                    <div style="padding-top:4px;">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrPlaylist" index="1" item_id=$item._item_id current=$item.playlist_rating_1_average_count|default:0 votes=$item.playlist_rating_1_count|default:0 }
                    </div>
                </div>
                <div style="display:table-cell;text-align:center;vertical-align:middle;">
                    <a onclick="popwin('{$jamroom_url}/radio_player/playlist_id={$item._item_id}/title={$item.playlist_title}','radio_player',805,625,true);">{jrCore_image image="button_player_play.png" width="32" height="32" alt=$item.playlist_title title=$item.playlist_title onmouseover="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play_hover.png');" onmouseout="$(this).attr('src','`$jamroom_url`/skins/`$_conf.jrCore_active_skin`/img/button_player_play.png');"}</a>
                </div>
            </div>

        </div>
    {/foreach}
{/if}
