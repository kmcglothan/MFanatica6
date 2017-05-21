{jrCore_module_url module="jrVideo" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrVideo" field="video_file" item=$item}

        </div>
        <h1>{$item.video_title}</h1>
        <div class="breadcrumbs">

            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            {if jrCore_module_is_active('jrCombinedVideo') && $item.quota_jrCombinedVideo_allowed == 'on'}
                <a href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedVideo"}">{jrCore_lang module="jrCombinedVideo" id=1 default="Videos"}</a>
            {else}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrVideo" id="35" default="Video"}</a>
            {/if}
            &raquo; {$item.video_title}

        </div>
    </div>

    <div class="block_content">

        <div class="item">

            <div class="container">
                <div class="row">
                    <div class="col12 last">

                        {* Make sure we're active *}
                        {if isset($item.video_active) && $item.video_active == 'off' && isset($item.quota_jrVideo_video_conversions) && $item.quota_jrVideo_video_conversions == 'on'}

                            <p class="center">{jrCore_lang module="jrVideo" id="38" default="This video file is currently being processed and will appear here when complete."}</p>

                        {elseif $item.video_file_extension == 'flv'}

                            {assign var="ap" value="`$_conf.jrCore_active_skin`_auto_play"}
                            {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                            {assign var="player_type" value=$_conf.$skin_player_type}
                            {assign var="player" value="jrVideo_`$player_type`"}
                            {if isset($player_type) && strlen($player_type) > 0}
                                {jrCore_media_player type=$player module="jrVideo" field="video_file" item=$item autoplay=$_conf.$ap}<br>
                            {else}
                                {jrCore_media_player module="jrVideo" field="video_file" item=$item autoplay=$_conf.$ap}<br>
                            {/if}

                            <div style="text-align:left;padding-left:6px">
                                {if strlen($item.video_album) > 0}
                                    <span class="info">{jrCore_lang module="jrVideo" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album}</a></span><br>
                                {/if}
                                {if isset({$item.video_category}) && strlen({$item.video_category}) > 0}
                                    <span class="info">{jrCore_lang module="jrVideo" id="12" default="category"}:</span> <span class="info_c">{$item.video_category}</span><br>
                                {/if}
                                <span class="info">{jrCore_lang module="jrVideo" id="37" default="streams"}:</span> <span class="info_c">{$item.video_file_stream_count|default:"0"|number_format}</span><br>
                                {if isset($item.video_file_original_extension)}
                                <span class="info">{jrCore_lang module="jrVideo" id="14" default="video file"}:</span> <span class="info_c">{$item.video_file_original_extension}, {$item.video_file_original_size|jrCore_format_size}, {$item.video_file_length}</span>
                                {else}
                                <span class="info">{jrCore_lang module="jrVideo" id="14" default="video file"}:</span> <span class="info_c">{$item.video_file_extension}, {$item.video_file_size|jrCore_format_size}, {$item.video_file_length}</span>
                                {/if}

                                <br><br>{jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                            </div>

                        {else}

                            <div class="center">
                                {jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline" alt=$item.video_title width=false height=false}<br>
                            </div>
                            <br>
                            <a href="{$jamroom_url}/{$murl}/download/video_file/{$item._item_id}">{jrCore_icon icon="download"}</a><br>
                            <div style="text-align:left;padding-left:6px">
                                <div style="padding-top:4px;">
                                    {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_count|default:0}
                                </div>
                            </div>

                        {/if}

                    </div>
                </div>
            </div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrVideo" item=$item}

    </div>

</div>
