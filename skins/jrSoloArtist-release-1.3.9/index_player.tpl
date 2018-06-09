{if isset($_items) && is_array($_items)}
    <div class="container">
        <div class="row">
            <div class="col12 last">
                <div class="capital" style="padding:0 0 10px 0;">
                    <h3>{$_items.0.audio_album}</h3>
                    {if jrUser_is_master()}
                        &nbsp;<a href="{$jamroom_url}/core/skin_admin/global/skin={$_conf.jrCore_active_skin}">{jrCore_image image="update.png" width="24" height="24" alt="Change Album" title="Change Album"}</a>
                    {/if}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col3">
                <div class="center" style="padding:3px 10px 5px 10px;">
                    {jrCore_module_url module="jrAudio" assign="murl"}
                    <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/albums/{$_items.0.audio_album_url}">{jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$_items.0._item_id size="large" crop="auto" class="img_shadow img_scale" alt=$_items.0.audio_title}</a>
                </div>
            </div>
            <div class="col6">
                <div class="center" style="padding-right:15px;">
                    {if isset($_conf.jrSoloArtist_auto_play) && $_conf.jrSoloArtist_auto_play == 'on'}
                        {assign var="ap" value="true"}
                    {else}
                        {assign var="ap" value="false"}
                    {/if}
                    {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                    {assign var="player_type" value=$_conf.$skin_player_type}
                    {assign var="player" value="jrAudio_`$player_type`"}
                    {if isset($player_type) && strlen($player_type) > 0}
                        {jrCore_media_player type=$player module="jrAudio" field="audio_file" items=$_items order_by="audio_file_track numerical_asc" autoplay=$ap}
                    {else}
                        {jrCore_media_player type="jrSolo_audio_player" module="jrAudio" field="audio_file" items=$_items order_by="audio_file_track numerical_asc" autoplay=$ap}
                    {/if}
                </div>
            </div>
            <div class="col3 last">
                {* see if this album has a bundle *}
                {jrFoxyCartBundle_get_album module="jrAudio" profile_id=$_items.0._profile_id name=$_items.0.audio_album assign="album"}
                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album image="index_album_add.png" width="124" height="26"}
                <br>
                <div class="media_title" style="font-size:10px;padding-bottom:8px;">
                    {$_items.0.audio_album} {jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="Tracks"}:&nbsp;
                {* We want to allow the item owner to re-order *}
                {if jrUser_is_master()}
                    <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/albums/{$_items.0.audio_album_url}#album">{jrCore_image image="reorder.png" width="24" height="24" alt="re-order `$_items.0.audio_album`" title="Re-Order  `$_items.0.audio_album`"}</a>
                {/if}
                </div>

                {if jrCore_is_mobile_device()}
                <div class="index_playlist_mobile">
                    <div class="index_playlist_container">
                        {foreach from=$_items item="item" name="loop"}
                            <div class="table-div" style="width:100%;padding-bottom:3px;border-bottom:1px dotted #FFF;margin:0 auto;">
                                <div class="table-row-div">
                                    <div class="table-cell-div center middle">
                                        {$item.list_rank}.&nbsp;
                                    </div>
                                    <div class="table-cell-div left middle no-break" style="padding-top:5px;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}"><span class="capital">{$item.audio_title}</span></a><br>
                                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_number|default:0}
                                    </div>
                                    <div class="table-cell-div right middle no-break" style="padding-top:5px;">
                                        {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                        {elseif $_conf.jrAudio_block_download != 'on'}
                                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="24"}</a></div>
                                        {else}
                                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="24"}</div>
                                        {/if}
                                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                 </div>
                {else}
                <div class="index_playlist">
                    <div id="container" class="index_playlist_container">


                        <!--INSERT CONTENT HERE-->
                        {foreach from=$_items item="item" name="loop"}
                            <div class="table-div" style="width:100%;padding-bottom:3px;border-bottom:1px dotted #FFF;margin:0 auto;">
                                <div class="table-row-div">
                                    <div class="table-cell-div center middle">
                                        {$item.list_rank}.&nbsp;
                                    </div>
                                    <div class="table-cell-div left middle no-break" style="padding-top:5px;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.audio_title_url}" title="{$item.audio_title}"><span class="capital">{$item.audio_title|truncate:15:"...":false}</span></a><br>
                                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_number|default:0}
                                    </div>
                                    <div class="table-cell-div right middle no-break" style="padding-top:5px;">
                                        {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                        {elseif $_conf.jrAudio_block_download != 'on'}
                                            <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="24"}</a></div>
                                        {else}
                                            <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="24"}</div>
                                        {/if}
                                        {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
                                    </div>
                                </div>
                            </div>
                        {/foreach}
                        <!--END CONTENT-->

                    </div>
                </div>

                <table style="max-width:275px;">
                    <tr>
                        <td>
                            <p style="text-align:right;">
                                <a onMouseover="move('container',5)" onMouseout="clearTimeout(move.to)">{jrCore_image image="up.png"}</a>
                                <a onMouseover="move('container',-5)" onMouseout="clearTimeout(move.to)">{jrCore_image image="down.png"}</a>
                            </p>
                        </td>
                    </tr>
                </table>
                {/if}

            </div>
        </div>
    </div>

{/if}
