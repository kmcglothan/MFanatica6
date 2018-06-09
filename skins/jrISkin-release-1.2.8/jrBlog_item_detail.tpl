{jrCore_module_url module="jrBlog" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrBlog" profile_url=$item.profile_url page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrBlog" profile_id=$item._profile_id item=$item}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div class="wrap clearfix">
                        <span class="title">{$item.blog_title}</span>
                        <span class="date">{$item.blog_publish_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p"}</span>
                        <div class="author clearfix">
                            <div class="author_image">
                                <a href="{$jamroom_url}/{$item.profile_url}">
                                    {jrCore_module_function
                                    function="jrImage_display"
                                    module="jrProfile"
                                    type="profile_image"
                                    item_id=$item._profile_id
                                    size="small"
                                    class="img_scale"
                                    crop="auto"
                                    alt=$item.blog_title
                                    }
                                </a>
                            </div>
                            {jrCore_lang skin="jrISkin" id="107" default="follow this author" assign="ft"}
                            <span><a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a> </span> {jrCore_module_function function="jrFollower_button" class="author_follow" profile_id=$item._profile_id title=$ft}  <br>
                            {$item.quota_jrProfile_name}
                        </div>
                        <div class="blog">
                            {if strlen($item.blog_image_size) > 0}
                                <div class="media_image">
                                    {jrCore_module_function
                                    function="jrImage_display"
                                    module="jrBlog"
                                    type="blog_image"
                                    item_id=$item._item_id
                                    size="xxxlarge"
                                    class="img_scale"
                                    crop="2:1"
                                    alt=$item.blog_title
                                    }
                                </div>
                            {/if}
                            {if strlen($item.blog_caption) > 0}
                                <div class="caption">
                                    {$item.blog_caption}
                                </div>
                            {/if}
                        </div>

                        <div class="media_text blog">
                            {$item.blog_text|jrBlog_readmore|jrCore_format_string:$item.profile_quota_id}
                        </div>
                        <br>
                        {if strlen($_conf.jrISkin_featured_story_ids) > 0}
                            <h2>{jrCore_lang skin="jrISkin" id="120" default="Recommended"}</h2>
                            <div class="recommended">
                                {jrCore_list module="jrBlog" search="_item_id in `$_conf.jrISkin_featured_story_ids`" order_by="_created RANDOM" limit="4" template="blog_recommend.tpl"}
                            </div>
                        {/if}
                    </div>
                </div>

                {* bring in module features *}
                <div class="action_feedback">
                    {jrISkin_feedback_buttons module="jrBlog" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrBlog_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrBlog"
                            index="1"
                            item_id=$item._item_id
                            current=$item.audio_rating_1_average_count|default:0
                            votes=$item.audio_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrBlog" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrISkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrBlog"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    require_image="blog_image"
                    template="chart_blog.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>






