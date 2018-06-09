{jrCore_module_url module="jrBlog" assign="murl"}
{if isset($_items)}
{foreach from=$_items item="item"}

<div class="block">
    <div class="block_config">
        {jrCore_item_create_button module="jrBlog" profile_id=$item._profile_id}
        {jrCore_item_update_button module="jrBlog" profile_id=$item._profile_id item_id=$item._item_id}
        {jrCore_item_delete_button module="jrBlog" profile_id=$item._profile_id item_id=$item._item_id}
    </div>
    <div class="title mb10">
        <h1>{$item.blog_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</a> &raquo; <a href="{$jamroom_url}/blogs">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="Blogs"}</a> &raquo; <a href="{$jamroom_url}/blogs/category/{$item.blog_category_url}">{$item.blog_category}</a> &raquo; {$item.blog_title}
        </div>
    </div>
    <div class="block_content">
        <div class="blogpost">

            <div class="blog_info" style="font-size: 0.8em">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" class="action_item_user_img iloutline" style="margin-right:12px"}
                <span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
                <span class="media_title">{jrCore_lang module="jrBlog" id="28" default="By"}:</span> <span class="normal">{$item.user_name}</span> <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}"><span class="normal">{$item.blog_category}</span></a><br>
                <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
            </div>

            <div class="normal p5">
                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    <div style="float:right">
                        {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width=false height=false class="iloutline img_shadow" style="margin-left:12px;margin-bottom:12px;"}
                    </div>
                {/if}
                {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
                <hr>
                <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <span class="normal capital"><a href="{$jamroom_url}/blogs/category/{$item.blog_category_url}">{$item.blog_category}</a></span>
                {if jrCore_module_is_active('jrComment')}
                    <span class="normal"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"} &raquo;</a></span>
                {/if}
            </div>

        </div>
        <a id="comments" name="comments"></a>
        {* bring in module features *}
        {jrCore_item_detail_features module="jrBlog" item=$item}
    </div>
</div>

{/foreach}
{/if}
