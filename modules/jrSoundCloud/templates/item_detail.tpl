{jrCore_module_url module="jrSoundCloud" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_detail_buttons module="jrSoundCloud" item=$item}

        </div>
        <h1>{$item.soundcloud_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            {if jrCore_module_is_active('jrCombinedAudio') && $item.quota_jrCombinedAudio_allowed == 'on'}
                <a href="{$jamroom_url}/{$item.profile_url}/{jrCore_module_url module="jrCombinedAudio"}">{jrCore_lang module="jrCombinedAudio" id=1 default="Audio"}</a>
            {else}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrSoundCloud" id="53" default="SoundCloud"}</a>
            {/if}
            &raquo; {$item.soundcloud_title}
        </div>
    </div>

    <div class="block_content">

        <div class="item">
            {jrSoundCloud_embed item_id=$item._item_id auto_play=true}<br>
            <span class="info">{jrCore_lang module="jrSoundCloud" id="26" default="Artist"}:</span> <span class="info_c">{$item.soundcloud_artist}</span>
            {if strlen($item.soundcloud_genre) > 0}
            <br><span class="info">{jrCore_lang module="jrSoundCloud" id="27" default="Genre"}:</span> <span class="info_c">{$item.soundcloud_genre}</span>
            {/if}
            <br><span class="info">{jrCore_lang module="jrSoundCloud" id="28" default="Duration"}:</span> <span class="info_c">{$item.soundcloud_duration}</span>
            <div style="padding-top:4px;">
                {jrCore_module_function function="jrRating_form" type="star" module="jrSoundCloud" index="1" item_id=$item._item_id current=$item.soundcloud_rating_1_average_count|default:0 votes=$item.soundcloud_rating_1_number|default:0}
            </div>
            <div class="clear"></div>
            {if strlen($item.soundcloud_description) > 0}
            <br>
            <span class="info">{jrCore_lang module="jrSoundCloud" id="38" default="Description"}:</span><br>
            <span class="info_c">{$item.soundcloud_description}</span><br>
            <br>
            {/if}
        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrSoundCloud" item=$item}

    </div>

</div>
