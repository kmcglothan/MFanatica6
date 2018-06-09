{jrCore_module_url module="jrYouTube" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMogul_breadcrumbs module="jrYouTube" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrYouTube" item=$item  field="youtube_file"}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrMogul_sort template="icons.tpl" nav_mode="jrYouTube" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                {jrYouTube_embed type="iframe" item_id=$item._item_id auto_play=$_conf.jrMogul_auto_play width="100%"}

                <div class="detail_box">
                    <div class="basic-info">
                        <div class="trigger"><span>{jrCore_lang skin="jrMogul" id="115" default="Basic Info"}</span></div>
                        <div class="item" style="display: none; padding: 0;">
                            <div style="display: table; width: 100%;">
                                <div class="header">
                                    <div>{jrCore_lang skin="jrMogul" id=41 default="Category"}</div>
                                    <div>{jrCore_lang skin="jrMogul" id=45 default="Duration"}</div>
                                    <div>{jrCore_lang skin="jrMogul" id=40 default="Created"}</div>
                                    <div>{jrCore_lang skin="jrMogul" id=38 default="Plays"}</div>
                                </div>
                                <div class="details">
                                    <div>{$item.youtube_category}</div>
                                    <div>{$item.youtube_duration}</div>
                                    <div>{$item._created|jrCore_date_format:"relative"}</div>
                                    <div>{$item.youtube_stream_count|jrCore_number_format}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if strlen($item.youtube_description) > 0}
                        <div class="description">
                            <div class="trigger"><span>{jrCore_lang skin="jrMogul" id="47" default="Description"}</span></div>
                            <div class="item" style="display: none;">
                                {$item.youtube_description|jrCore_string_to_url}
                            </div>
                        </div>
                    {/if}
                </div>
                {* bring in module features *}
                <div class="action_feedback">
                    {jrMogul_feedback_buttons module="jrYouTube" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrYouTube_{$item._item_id}_rating">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrYouTube" index="1" item_id=$item._item_id current=$item.youtube_rating_1_average_count|default:0 votes=$item.youtube_rating_1_number|default:0}
                        </div>
                    {/if}
                    {jrCore_item_detail_features module="jrYouTube" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrMogul_sort template="icons.tpl" nav_mode="jrYouTube" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrMogul" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrYouTube"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_youtube.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
