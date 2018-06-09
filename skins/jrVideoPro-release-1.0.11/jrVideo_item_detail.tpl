{jrCore_module_url module="jrVideo" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
<div class="profile_minimal">
    <div class="profile_info">
        <div class="wrap">
            <div class="table">
                <div class="table-row">
                    <div class="table-cell profile-image">
                        <div class="profile_image">
                            <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function
                            function="jrImage_display"
                            module="jrProfile"
                            type="profile_image"
                            item_id=$item._profile_id
                            size="xxlarge"
                            crop="auto"
                            class="img_scale img_shadow"
                            alt=$item.profile_name
                            width=false
                            height=false}</a>
                        </div>
                    </div>
                    <div class="table-cell">
                        <div class="profile_name">
                            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name|truncate:55}</a><br>
                            <span><a href="{$jamroom_url}/{$item.profile_url}">@{$item.profile_url}</a> </span>
                        </div>
                    </div>
                    <div class="table-cell action_buttons">
                        {jrCore_lang id=5 skin="jrVideoPro" default="Follow" assign="Follow"}
                        {jrFollower_button profile_id=$item._profile_id title=$follow}
                        {jrCore_item_detail_buttons module="jrVideo" item=$item field="video_file"}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<section id="video_play">
    <div class="wrap">
        {if isset($item.video_active) && $item.video_active == 'off' && isset($item.quota_jrVideo_video_conversions) && $item.quota_jrVideo_video_conversions == 'on'}
            <p class="center waiting">{jrCore_lang module="jrVideo" id="38" default="This video file is currently being processed and will appear here when complete."}</p>
        {elseif $item.video_file_extension == 'm4v'}
            {jrCore_media_player module="jrVideo" field="video_file" item=$item autoplay=$_conf.jrVideoPro_auto_play}
        {/if}
    </div>
</section>
<section class="detail_box">
    <div class="wrap">
        <div class="row">
            <div class="col6">
                {jrCore_lang skin="jrVideo" id=67 default="My Videos" assign="my_vids"}
                <h1><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.video_album_url}">{$item.video_album|default:$my_vids}</a> &middot; {$item.video_title}</h1>
                {jrCore_lang skin="jrVideoPro" id=60 default="released"} {$item._created|jrCore_date_format:"relative"} &middot; {$item.video_file_stream_count|jrCore_number_format} {jrCore_lang skin="jrVideoPro" id=58 default="views"}<br>
                <span class="italic">{$item.video_category}</span>
            </div>
            <div class="col6">
                {jrShareThis module="jrVideo" item=$item template="shareThis.tpl"}<br>
                <div class="text_right">

                    {if jrCore_module_is_active('jrLike')}
                        {if $_conf.jrLike_like_option == 'all' || $_conf.jrLike_like_option == 'like'}
                            {jrLike_button action="like" item=$item module="jrVideo" item_id=$item_id}
                        {/if}

                        {if $_conf.jrLike_like_option == 'all' || $_conf.jrLike_like_option == 'dislike'}
                            {jrLike_button action="dislike" item=$item module="jrVideo" item_id=$item_id}
                        {/if}
                    {/if}

                    <span class="comment_button">
                        <a href="#" onclick="jrVideoPro_modal('#jrVideo_{$item._item_id}_comment')">
                            {jrCore_image image="comment.png" width="24px" height="auto"}
                            {$item.video_comment_count|jrCore_number_format}
                        </a>
                    </span>
                    <div class="like_button_box">
                        {if jrUser_is_logged_in()}
                        <a onclick="jrAction_share('jrVideo','{$item._item_id}')">
                            {else}
                            <a href="{$jamroom_url}/{$uurl}/login">
                                {/if}
                                {jrCore_lang module="jrAction" id=34 default="Share This with your Followers" assign="title"}
                                {jrCore_image image="share.png" width="24" height="24" class="like_button_img" alt=$title title=$title}</a>
                            <span><a>{$item.video_share_count|jrCore_number_format}</a> </span>
                    </div>
                    {if jrCore_module_is_active('jrTags')}
                        <div class="like_button_box">
                            {if jrUser_is_logged_in()}
                            <a onclick="jrVideoPro_open_div('#jrVideo_{$item._item_id}_tag', '#jrVideo_{$item._item_id}_rating')">
                                {else}
                                <a href="{$jamroom_url}/{$uurl}/login">
                                    {/if}
                                    {jrCore_lang module="jrTags" id="2" default="Tag" assign='tag'}
                                    {jrCore_image image="tag.png" width="24" height="24" class="like_button_img" alt='Tags' title=$tag}
                                </a>
                                <span><a>{$item.video_tag_count|jrCore_number_format}</a></span>
                        </div>
                    {/if}
                    {if jrCore_module_is_active('jrRating') && $timeline != true}
                        <div class="like_button_box">
                            <a onclick="jrVideoPro_open_div('#jrVideo_{$item._item_id}_rating', '#jrVideo_{$item._item_id}_tag')">
                                {jrCore_lang module="jrRating" id="1" default="Rate" assign='rate'}
                                {jrCore_image image="star.png" width="24" height="24" class="like_button_img" alt='Rate' title=$rate}</a>
                            <span><a>{$item.video_rating_1_count|jrCore_number_format}</a></span>
                        </div>
                    {/if}
                    <br>
                    <div id="jrVideo_{$item._item_id}_tag" style="display: none;">
                        {jrTags_add module="jrVideo" item_id=$item._item_id profile_id=$item._item_id}
                    </div>
                    <div id="jrVideo_{$item._item_id}_rating" style="display: none;">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$item._item_id current=$item.video_rating_1_average_count|default:0 votes=$item.video_rating_1_number|default:0 }
                    </div>
                    <div id="jrVideo_{$item._item_id}_comment" style="display: none;" class="jrVideoPro_modal">
                        <span class="simplemodal-close">x</span>
                        {jrComment_form module="jrVideo" item=$item item_id=$item._item_id profile_id=$item.profile_id}
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
<div class="videos">
    <div class="wrap">
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/">{jrCore_lang skin="jrVideoPro" id=55 default="More From"} {$item.profile_name}</a></span>
            </div>
        </div>
        <div class="list_wrap">
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_asc"
                        limit="18"
                        profile_id=$item._profile_id
                        template="index_item_1.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </div>
        <br>
        <div class="row">
            <div class="head">
                <span><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/">{jrCore_lang skin="jrVideoPro" id=56 default="New Series From"} {$item.profile_name}</a></span>
            </div>
        </div>
        <div class="list_wrap">
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_asc"
                        limit="18"
                        profile_id=$item._profile_id
                        group_by="video_album_url"
                        template="index_item_1.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </div>
        <br>
        <div class="row">
            <div class="head">
                <span> <a href="{$jamroom_url}/{$murl}/">{jrCore_lang skin="jrVideoPro" id=57 default="You May Also Like"}</a></span>
            </div>
        </div>
        <div class="list_wrap">
            <div class="row">
                <div class="index_list clearfix page_1">
                    <div>{jrCore_list
                        module="jrVideo"
                        order_by="_item_id numerical_asc"
                        limit="18" search="_profile_id != `$item._profile_id`"
                        template="index_item_1.tpl"
                        require_image="video_image"
                        }
                    </div>
                </div>
            </div>
            <a class="list_nav previous"></a>
            <a class="list_nav next"></a>
        </div>
    </div>
</div>

