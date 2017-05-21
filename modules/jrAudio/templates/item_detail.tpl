{jrCore_module_url module="jrAudio" assign="murl"}
{jrCore_module_url module="jrImage" assign="iurl"}
{assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
{assign var="player_type" value=$_conf.$skin_player_type}

<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrAudio" field="audio_file" item=$item}

        </div>
        <h1>{$item.audio_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
                <a href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedAudio"}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
            {else}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrAudio" id="41" default="Audio"}</a>
            {/if}
            &raquo; {$item.audio_title}
        </div>
    </div>

    {if $player_type == 'gray_overlay_player' || $player_type == 'black_overlay_player'}

    <div class="block_content">
        <div class="item">
            <div class="container">
                <div class="row">

                    <div class="col3">
                        <a href="{$jamroom_url}/{$murl}/{$iurl}/audio_image/{$item._item_id}/1280/_v={$item.audio_image_time}" data-lightbox="images" title="{$item.audio_title|jrCore_entity_string}">
                        {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="xlarge" crop="square" class="iloutline img_shadow img_scale" alt=$item.audio_title}
                        </a>
                    </div>

                    <div class="col9 last">
                        <div style="position:relative;padding:0 20px;height:203px;">

                            {if isset($item.audio_active) && $item.audio_active == 'off' && isset($item.quota_jrAudio_audio_conversions) && $item.quota_jrAudio_audio_conversions == 'on'}
                                <p class="center">{jrCore_lang module="jrAudio" id="40" default="This audio file is currently being processed and will appear here when complete."}</p>
                            {else}
                                <h1>{$item.audio_title}</h1><br><br>
                                <h3>{jrCore_lang module="jrAudio" id="31" default="album"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></h3><br>
                                <h3>{jrCore_lang module="jrAudio" id="12" default="genre"}: {$item.audio_genre}</h3>
                                {if $item.audio_file_extension == 'mp3'}
                                    <br><h3>{jrCore_lang module="jrAudio" id="51" default="streams"}: {$item.audio_file_stream_count|default:"0"|jrCore_format_number}</h3>
                                    {if !empty($item.audio_file_item_price)}
                                        <br><h3>{jrCore_lang module="jrAudio" id="14" default="audio file"}: <span style="text-transform:uppercase">{$item.audio_file_original_extension}</span>, {$item.audio_file_original_size|jrCore_format_size}, {$item.audio_file_length}</h3>
                                    {/if}
                                    <br>
                                {/if}

                                {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}

                                {if $item.audio_file_extension == 'mp3'}
                                    {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                                    {assign var="player" value="jrAudio_`$player_type`"}
                                    <br>
                                    {if jrCore_is_mobile_device()}
                                    <div style="position:absolute;bottom:0;width:88%">
                                    {else}
                                    <div style="position:absolute;bottom:0;width:95%">
                                    {/if}
                                        {jrCore_media_player type=$player module="jrAudio" field="audio_file" item=$item autoplay=$_conf.$ap}
                                    </div>
                                {/if}
                            {/if}

                        </div>
                    </div>

                </div>
            </div>
        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrAudio" item=$item}

    </div>

{else}

    <div class="block_content">

        <div class="item">

            <div class="jraudio_detail_player">
                <div class="jraudio_detail_player_left">

                    {* Make sure we're active *}
                    {if isset($item.audio_active) && $item.audio_active == 'off' && isset($item.quota_jrAudio_audio_conversions) && $item.quota_jrAudio_audio_conversions == 'on'}

                        <p class="center">{jrCore_lang module="jrAudio" id="40" default="This audio file is currently being processed and will appear here when complete."}</p>

                    {elseif $item.audio_file_extension == 'mp3'}

                        {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                        {assign var="player" value="jrAudio_`$player_type`"}
                        {if isset($player_type) && strlen($player_type) > 0}
                            {jrCore_media_player type=$player module="jrAudio" field="audio_file" item=$item autoplay=$_conf.$ap}<br>
                        {else}
                            {jrCore_media_player module="jrAudio" field="audio_file" item=$item autoplay=$_conf.$ap}<br>
                        {/if}

                        <div style="text-align:left;padding-left:6px">
                            <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}">{$item.audio_album}</a></span><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$item.audio_genre}</span><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="51" default="streams"}:</span> <span class="info_c">{$item.audio_file_stream_count|default:"0"|number_format}</span><br>
                            {if !empty($item.audio_file_item_price)}
                                <span class="info">{jrCore_lang module="jrAudio" id="54" default="purchase"}:</span> <span class="info_c">{$item.audio_file_original_extension}, {$item.audio_file_original_size|jrCore_format_size}, {$item.audio_file_length}</span>
                            {/if}
                            <br>{jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}
                        </div>

                    {else}

                        {* allow downloads if we are not blocked *}
                        {if isset($_conf.jrAudio_block_download) && $_conf.jrAudio_block_download == 'off'}
                            <a href="{$jamroom_url}/{$murl}/download/audio_file/{$item._item_id}">{jrCore_icon icon="download"}</a><br>
                        {/if}

                        <div style="text-align:left;padding-left:6px">
                            <span class="info">{jrCore_lang module="jrAudio" id="31" default="album"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.audio_album_url}"><span class="info_c">{$item.audio_album}</span></a><br>
                            <span class="info">{jrCore_lang module="jrAudio" id="12" default="genre"}:</span> <span class="info_c">{$item.audio_genre}</span><br>
                            {jrCore_module_function function="jrRating_form" type="star" module="jrAudio" index="1" item_id=$item._item_id current=$item.audio_rating_1_average_count|default:0 votes=$item.audio_rating_1_count|default:0}
                        </div>

                    {/if}
                </div>

                <div class="jraudio_detail_player_right">
                    {jrCore_module_function function="jrImage_display" module="jrAudio" type="audio_image" item_id=$item._item_id size="large" class="iloutline img_shadow" alt=$item.audio_title width=false height=false}
                </div>

            </div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrAudio" item=$item}

    </div>

{/if}

</div>
