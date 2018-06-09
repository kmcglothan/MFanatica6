{jrCore_module_url module="jrVideo" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrVideo" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrVideo" item=$item field="video_file"}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                {if isset($item.video_active) && $item.video_active == 'off' && isset($item.quota_jrVideo_video_conversions) && $item.quota_jrVideo_video_conversions == 'on'}
                    <p class="center waiting">{jrCore_lang module="jrVideo" id="38" default="This video file is currently being processed and will appear here when complete."}</p>
                {elseif $item.video_file_extension == 'm4v'}
                    {jrCore_media_player module="jrVideo" field="video_file" item=$item autoplay=$_conf.jrCelebrity_auto_play}
                {/if}
                <div class="detail_box">
                    <div class="basic-info">
                        <div class="trigger"><span>{jrCore_lang skin="jrCelebrity" id="115" default="Basic Info"}</span></div>
                        <div class="item" style="display: none; padding: 0; margin: 5px auto 0;">
                            <div style="display: table; width: 100%;">
                                <div class="header">
                                    <div>{jrCore_lang skin="jrCelebrity" id=21 default="Album"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=41 default="Category"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=40 default="Created"}</div>
                                    <div>{jrCore_lang skin="jrCelebrity" id=38 default="Plays"}</div>
                                </div>
                                <div class="details">
                                    <div>{$item.video_album}</div>
                                    <div>{$item.video_category}</div>
                                    <div>{$item._created|jrCore_date_format:"relative"}</div>
                                    <div>{$item.video_file_stream_count|jrCore_number_format}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if strlen($item.video_description) > 0}
                        <div class="description">
                            <div class="trigger"><span>{jrCore_lang skin="jrCelebrity" id="47" default="Description"}</span></div>
                            <div class="item" style="display: none;">
                                {$item.video_description}
                            </div>
                        </div>
                    {/if}
                </div>
                {* bring in module features *}
                <div class="action_feedback">
                    {jrCelebrity_feedback_buttons module="jrVideo" item=$item}
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
                    {jrCore_item_detail_features module="jrVideo" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        <ul id="actions_tab">
            <li class="solo" id="channels_tab">
                <a href="#"></a>
            </li>
        </ul>
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrVideo"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    require_image="video_image"
                    template="chart_video.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

