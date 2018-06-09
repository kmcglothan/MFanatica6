{* Show our audio items in this album *}

{if isset($_items) && is_array($_items)}

    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="block">

        <div class="block_config">
        {if jrCore_module_is_active('jrFoxyCart')}
            {jrFoxyCartBundle_get_album module="jrAudio" profile_id=$_items.0._profile_id name=$_items.0.audio_album assign="album"}
            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$album.bundle_item_price no_bundle="true" item=$album}
        {/if}
        {* We want to allow the item owner to re-order *}
        {if jrUser_is_master()}
            <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/albums/{$_items.0.audio_album_url}#album">{jrCore_image image="reorder.png" width="24" height="24" alt="re-order `$_items.0.audio_album`" title="Re-Order `$_items.0.audio_album`"}</a>
        {/if}
        </div>

        <div class="title">
            <h1>{$_items.0.audio_album}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/"><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</span></a> &raquo; <a href="{$jamroom_url}/music">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Music"}</a> &raquo; <a onclick="jrLoad('#details','{$jamroom_url}/albums_list');">{jrCore_lang module="jrAudio" id="34" default="Albums"}</a> &raquo; {$_items.0.audio_album}
            </div>
        </div>

        <div class="block_content">

            <div class="item">
                <div class="jraudio_detail_player">
                    <div class="jraudio_detail_player_left" style="width:50%;">
                        <div class="center">
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
                                {jrCore_media_player type="jrAudio_audio_player" module="jrAudio" field="audio_file" items=$_items order_by="audio_file_track numerical_asc" autoplay=$ap}
                            {/if}
                        </div>
                    </div>
                    <div class="jraudio_detail_player_right" style="width:50%;text-align:center;vertical-align:top;">
                        {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$_items.0._item_id size="xlarge" class="iloutline img_shadow" alt=$_items.0.audio_title width=false height=false}<br>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="block">
        <div class="block_content">
            {foreach from=$_items item="item" name="loop"}
                <div class="item">
                    <div class="container">
                        <div class="row">
                            <div class="col1">
                                <div class="rank">
                                    {$item@iteration}
                                </div>
                            </div>
                            <div class="col8">
                                <div class="p5">
                                    <a onclick="jrLoad('#details','{$jamroom_url}/music_list/{$item._item_id}');"><span class="capital bold">{$item.audio_title}</span></a>
                                    <br>{jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_number|default:0 }
                                </div>
                            </div>
                            <div class="col3 last">
                                <div class="p5 center">
                                    {if isset($item.audio_file_item_price) && $item.audio_file_item_price > 0}
                                        {if jrCore_module_is_active('jrFoxyCart')}
                                            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrAudio" field="audio_file" item=$item}
                                        {elseif jrCore_module_is_active('jrPayPal')}
                                            {jrPayPal_buy_now_button module="jrAudio" item=$item}
                                        {/if}
                                    {elseif $_conf.jrAudio_block_download != 'on'}
                                        <div class="add_to_cart_section" title="Free Download"><span class="add_to_cart_price">Free</span><a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}" title="download">{jrCore_icon icon="download" size="24"}</a></div>
                                    {else}
                                        <div class="add_to_cart_section" title="Download Not Available"><span class="add_to_cart_price">N/A</span>{jrCore_icon icon="lock" size="24"}</div>
                                    {/if}
                                    {jrCore_module_function function="jrPlaylist_button" playlist_for="jrAudio" item_id=$item._item_id}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>

{/if}
