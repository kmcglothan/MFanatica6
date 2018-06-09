{jrCore_module_url module="jrAudio" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrAudio" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrAudio" item=$item  field="audio_file"}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                {if isset($item.audio_active) && $item.audio_active == 'off' && isset($item.quota_jrAudio_audio_conversions) && $item.quota_jrAudio_audio_conversions == 'on'}
                    <div class="item" style="text-align: center;">
                        <p class="pending">{jrCore_lang module="jrAudio" id="40" default="This audio file is currently being processed and will appear here when complete."}</p>
                    </div>
                {elseif $item.audio_file_extension == 'mp3'}
                    {jrCore_media_player module="jrAudio" field="audio_file" item=$item autoplay=$_conf.jrCelebrity_auto_play}
                {/if}
                <div class="detail_box">
                    <div class="basic-info">
                        <div class="trigger"><span>{jrCore_lang skin="jrCelebrity" id="115" default="Basic Info"}</span></div>
                        <div class="item" style="display: none; padding: 0; margin: 5px auto 0;">
                            <div style="display: table; width: 100%;">
                                <div class="header">
                                    <div>{jrCore_lang skin="jrCelebrity" id=21 default="Album"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=39 default="Genre"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=40 default="Created"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=38 default="Plays"}</div>
                                </div>
                                <div class="details">
                                    <div>{$item.audio_album}</div>
                                    <div>{$item.audio_genre}</div>
                                    <div>{$item._created|jrCore_date_format:"relative"}</div>
                                    <div>{$item.audio_file_stream_count|jrCore_number_format}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if strlen($item.audio_text) > 0}
                        <div class="description">
                            <div class="trigger"><span>{jrCore_lang skin="jrCelebrity" id="47" default="Description"}</span></div>
                            <div class="item" style="display: none;">
                                {$item.audio_text}
                            </div>
                        </div>
                    {/if}
                    {if strlen($item.audio_lyrics) > 0}
                        <div class="lyrics">
                            <div class="trigger"><span>{jrCore_lang skin="jrCelebrity" id="116" default="Lyrics"}</span></div>
                            <div class="item" style="display: none;">
                                {$item.audio_lyrics}
                            </div>
                        </div>
                    {/if}
                </div>
                {* bring in module features *}
                <div class="action_feedback" style="padding: 0">
                    {jrCelebrity_feedback_buttons module="jrAudio" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrAudio"
                            index="1"
                            item_id=$item._item_id
                            current=$item.audio_rating_1_average_count|default:0
                            votes=$item.audio_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrAudio" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrAudio"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_audio.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

