{jrProfile_disable_header}
{jrProfile_disable_sidebar}

{jrCore_module_url module="jrGroupPage" assign="murl"}


<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrGroupPage" profile_url=$item.profile_url profile_name=$profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrGroupPage" item=$item}
    </div>
</div>

<div class="box">
    {jrCelebrity_sort template="icons.tpl" nav_mode="jrGroupPage" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap detail_section">
            <div class="media">
                <div class="wrap">
                    <span class="title">{$item.npage_title}</span>
                    {$item.npage_body|jrCore_format_string:$item.profile_quota_id}
                </div>
            </div>


            <div class="action_feedback" style="padding: 0">
                {jrCelebrity_feedback_buttons module="jrGroupPage" item=$item}
                {if jrCore_module_is_active('jrRating')}
                    <div class="rating" id="jrGroupPage_{$item._item_id}_rating">{jrCore_module_function
                        function="jrRating_form"
                        type="star"
                        module="jrGroupPage"
                        index="1"
                        item_id=$item._item_id
                        current=$item.npage_rating_1_average_count|default:0
                        votes=$item.npage_rating_1_number|default:0}</div>
                {/if}
                {jrCore_item_detail_features module="jrGroupPage" item=$item}
            </div>

        </div>
    </div>
</div>

