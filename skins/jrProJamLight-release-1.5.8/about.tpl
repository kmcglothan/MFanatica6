<div style="height:588px;overflow:auto;">
    {* ROW TEMPLATE *}
    {capture name="row_template" assign="site_about_template"}
        {literal}
            {if isset($_items)}
            {jrCore_module_url module="jrEvent" assign="murl"}
            {foreach from=$_items item="item"}
            <div style="padding:10px;">
                <div class="br-info" style="margin-bottom:20px;">
                    <div class="blog-div">
                        <span class="blog-user capital"> By {$item.profile_name}</span>
                    </div>
                    <div class="blog-div">
                        <span class="blog-date"> {$item.blog_publish_date|jrCore_format_time}</span>
                    </div>
                    <div class="blog-div">
                        <span class="blog-tag capital"> Tag: {$item.blog_category}</span>
                    </div>
                    {if jrUser_is_master()}
                    <div class="float-right">
                        {jrCore_item_update_button module="jrBlog" profile_id=$item._profile_id item_id=$item._item_id}
                    </div>
                    {/if}
                    <div class="clear"></div>
                </div>
                <h3>{$item.blog_title}</h3>
                <div class="blog-text">
                    {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width="128" height="128" crop="auto" class="iloutline img_shadow" style="float:left;margin-right:8px;margin_bottom:8px;"}
                    {/if}
                    {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
                </div>
            </div>
            {/foreach}
            {else}
            <div style="padding:10px;">
                {if jrUser_is_master() || jrUser_is_admin()}
                <div class="br-info" style="margin-bottom:20px;">
                    <h3>No About Page Setup</h3>
                    <div class="float-right">
                        <a onclick="jrCore_window_location('{$jamroom_url}/{$_user.profile_url}/blog');">{jrCore_icon icon="gear"}</a>
                    </div>
                    <div class="clear"></div>
                </div>
                <div class="blog-text">
                    Click the button with the "+" image to create an about blog, be sure to set the category to about so it shows here.
                </div>
                {else}
                <div class="br-info" style="margin-bottom:20px;">
                    <h3>About Us</h3>
                </div>
                <div class="blog-text">
                    Coming Soon!
                </div>
                {/if}
            </div>
            {/if}
        {/literal}
    {/capture}

    {* EVENT LIST FUNCTION *}
    {jrCore_list module="jrBlog" order_by="_created desc" limit="1" search1="_user_id = 1"  search2="blog_category = about" template=$site_about_template}
</div>
