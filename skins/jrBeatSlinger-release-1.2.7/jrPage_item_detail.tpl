{jrCore_module_url module="jrPage" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrPage" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrPage" item=$item }
    </div>
</div>

<div class="box">
    {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrPage" profile_url=$profile_url}

    <div class="box_body">
        <div class="wrap detail_section">
            <div class="media">
                <div class="wrap">
                    {$item.page_body|jrCore_format_string:$item.profile_quota_id:null:nl2br}
                </div>
            </div>

            {* bring in module features *}
            {if $_post.module_url != 'page'}
                {* bring in module features *}
                <div class="action_feedback">
                    {* bring in module features if enabled *}
                    {if !isset($item.page_features) || $item.page_features == 'on'}
                        {jrBeatSlinger_feedback_buttons module="jrPage" item=$item}
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
                        {jrCore_item_detail_features module="jrPage" item=$item}
                    {/if}
                </div>
            {/if}
        </div>
    </div>
</div>


