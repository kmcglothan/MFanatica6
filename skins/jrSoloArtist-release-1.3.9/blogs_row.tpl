{jrCore_module_url module="jrBlog" assign="murl"}

<div class="block">
    {if isset($_post.option) && $_post.option == 'category'}
        {*this is the CATEGORY page, it doesnt show the index header so no breadcrumbs.  needs its own header.*}
        <div class="block_config">
            {jrCore_item_create_button module="jrBlog" profile_id=$_profile_id}
        </div>
        <div class="title mb10">
            <h1 style="text-transform:capitalize;">{$_items[0].blog_category}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="Blogs"}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Home"}</a> &raquo; <a href="{$jamroom_url}/blogs">{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="Blogs"}</a> &raquo; {$_items[0].blog_category}
            </div>
        </div>

    {else}

        <div class="title mb10">
            <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="Blogs"}:</h1>
        </div>

    {/if}
    <div class="block_content">

        {if isset($_items)}
            {foreach from=$_items item="item"}
                <div class="item">
                    <div class="blogpost">
                        {if jrUser_is_master()}
                            <div style="float: right">
                                <a onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}');">{jrCore_icon icon="gear"}</a>
                            </div>
                        {/if}

                        <h2><a href="{$jamroom_url}/blogs/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                        <br>
                        {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
                        <div class="normal p5">
                            {$item.blog_text|truncate:200:"...":false|jrCore_format_string:$item.profile_quota_id}
                        </div>
                        <hr>
                        <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <span class="normal capital"><a href="{$jamroom_url}/blogs/category/{$item.blog_category_url}">{$item.blog_category}</a></span>
                        {if jrCore_module_is_active('jrComment')}
                            <span class="normal"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"} &raquo;</a></span>
                        {/if}

                    </div>
                </div>
            {/foreach}
        {/if}

    </div>

</div>

