{jrCore_module_url module="jrGroupDiscuss" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}


<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrGroupDiscuss" profile_url=$profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {if jrUser_is_logged_in() && $item._user_id == $_user._user_id && !jrCore_checktype($item['discuss_comment_count'], 'number_nz')}
            <a href="{$jamroom_url}/{$murl}/update/id={$item._item_id}">{jrCore_icon icon="gear"}</a>
        {else}
            {jrCore_item_detail_buttons module="jrGroupDiscuss" item=$item}
        {/if}
    </div>
</div>

<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrGroupDiscuss" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap detail_section">
            <div id="list">
                <div class="item">
                    <div class="container">
                        <div class="row">
                            <div class="col2">
                                <div class="p10 center">
                                    {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="medium" title="{$item.user_name}" alt="{$item.user_name}" class="img_scale" _v=$item._updated}<br>
                                    <small>{$item._created|jrCore_format_time}</small><br>
                                    <a href="{$jamroom_url}/{$item.original_profile_url}">@{$item.original_profile_url}</a>
                                </div>
                            </div>
                            <div class="col10 last">
                                <div class="p10" style="padding: 0 1em;">
                                    <span class="title">{$item.discuss_title}</span>
                                    {$item.discuss_description|jrCore_format_string:$item.profile_quota_id}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            {* bring in module features *}
            {if jrUser_is_logged_in()}
                <div class="action_feedback" style="padding: 0">
                    {if jrGroup_member_has_access($item)}
                        {jrBeatSlinger_feedback_buttons module="jrGroupDiscuss" item=$item}
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
                        {jrCore_item_detail_features module="jrGroupDiscuss" item=$item}
                    {/if}
                </div>
            {/if}

        </div>
    </div>
</div>
