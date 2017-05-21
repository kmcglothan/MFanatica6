{jrCore_module_url module="jrAudio" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}

        <div class="block">
            <div class="block_config">
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
            <div class="title">
                <h1>{$item.audio_title}</h1>
                <div class="breadcrumbs">
                    <a href="{$jamroom_url}/"><span class="capital">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</span></a> &raquo; <a href="{$jamroom_url}/music">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Music"}</a> &raquo; {$item.audio_title}
                </div>
            </div>
            <div class="block_content">
                <div class="item">

                    <div class="jraudio_detail_player">
                        <div class="jraudio_detail_player_left" style="width:50%;">

                            {* Make sure we're active *}
                            {if isset($item.audio_active) && $item.audio_active == 'off' && isset($item.quota_jrAudio_audio_conversions) && $item.quota_jrAudio_audio_conversions == 'on'}

                                <p class="center">{jrCore_lang module="jrAudio" id="40" default="This audio file is currently being processed and will appear here when complete."}</p>

                            {elseif $item.audio_file_extension == 'mp3'}

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
                                        {jrCore_media_player type=$player module="jrAudio" field="audio_file" item=$item autoplay=$ap}<br>
                                    {else}
                                        {jrCore_media_player type="jrAudio_solo_player" module="jrAudio" field="audio_file" item=$item autoplay=$ap}<br>
                                    {/if}
                                </div>
                                <div style="text-align:left;padding-left:6px">
                                    <span class="media_title">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="normal">{$item.audio_genre}</span><br>
                                    <span class="media_title">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <a onclick="jrLoad('#details','{$jamroom_url}/album_list/{$item.audio_album_url}');"><span class="capital" style="font-size:12px;">{$item.audio_album}</span></a><br>
                                    <span class="media_title">{jrCore_lang module="jrAudio" id="51" default="streams"}:</span> <span class="normal">{$item.audio_file_stream_count|default:"0"|number_format}</span>
                                    <div style="padding-top:12px;">
                                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                                    </div>
                                </div>

                            {else}

                                <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_image image="download.png" alt="download" title="download"}</a><br>
                                <div style="text-align:left;padding-left:6px">
                                    <span class="media_title">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="normal">{$item.audio_genre}</span><br>
                                    <span class="media_title">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="capital" style="font-size:12px;">{$item.audio_album}</span></a>
                                    <div style="padding-top:4px;">
                                        {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0 }
                                    </div>
                                </div>

                            {/if}
                        </div>

                        <div class="jraudio_detail_player_right" style="width:50%;text-align:center;vertical-align:top;">
                            {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" class="iloutline img_shadow" alt=$item.audio_title width=false height=false}
                        </div>

                    </div>

                </div>

                {* Are comments enabled for this song? *}
                {jrComment_form module="jrAudio" profile_id=$item._profile_id item_id=$item._item_id}

                {if jrCore_module_is_active('jrDisqus')}
                    <div class="item">
                        {jrDisqus_comments disqus_identifier="jrAudio_`$item._item_id`"}
                    </div>
                {/if}

            </div>
        </div>

    {/foreach}
{/if}
