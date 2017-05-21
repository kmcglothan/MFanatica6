{jrCore_module_url module="jrBlog" assign="murl"}

{if isset($_post.option) && $_post.option == 'categories'}

    <div class="item">
        <div class="p5">
            {if isset($_items)}
                <div class="container">
                    <div class="row">
                        {foreach from=$_items item="item"}
                            <div class="col3">
                                <div class="p5 center">
                                    <div class="form_button" onclick="jrCore_window_location('{$jamroom_url}/blogs/category/{$item.blog_category_url|default:"default"}');" style="cursor:pointer;">{$item.blog_category|truncate:20:"...":false|default:"default"}</div>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}
        </div>
    </div>

{elseif isset($_post.option) && $_post.option == 'search'}

    {if isset($_items)}
        {foreach from=$_items item="item"}
            <div class="item">

                {if jrUser_is_master() && jrUser_is_admin()}
                    <div class="block_config">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear"}</a>
                    </div>
                {/if}

                <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                <br>
                <span class="info">{jrCore_lang module="jrBlog" id="28" default="By"}:</span> <span class="info_c">{$item.user_name}</span> <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <a href="{$jamroom_url}/blogs/category/{$item.blog_category_url}"><span class="info_c">{$item.blog_category}</span></a>&nbsp;
                {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$item.blog_publish_date|jrCore_format_time}</span>&nbsp;
                <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
                <div class="normal p5">

                    {$item.blog_text|jrCore_format_string:$item.profile_quota_id}

                </div>
            </div>
            <hr>
        {/foreach}
    {/if}

{else}

    {if isset($_items)}
        {foreach from=$_items item="item"}
            <div class="item">

                {if jrUser_is_master() && jrUser_is_admin()}
                <div class="block_config">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear"}</a>
                </div>
                {/if}

                <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                <br>
                <span class="info">{jrCore_lang module="jrBlog" id="28" default="By"}:</span> <span class="info_c">{$item.user_name}</span> <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <a href="{$jamroom_url}/blogs/category/{$item.blog_category_url}"><span class="info_c">{$item.blog_category}</span></a>&nbsp;
                {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$item.blog_publish_date|jrCore_format_time}</span>&nbsp;
                <span style="display:inline-block;margin-top:6px;">{jrCore_module_function function="jrRating_form" type="star" module="jrBlog" index="1" item_id=$item._item_id current=$item.blog_rating_1_average_count|default:0 votes=$item.blog_rating_1_count|default:0}</span>
                <div class="normal p5">

                    {$item.blog_text|truncate:250:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}

                </div>
            </div>
            <hr>
        {/foreach}
    {/if}

{/if}
