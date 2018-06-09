{jrCore_module_url module="jrBlog" assign="murl"}
{if is_array($_items)}
{foreach from=$_items item="item"}

<div class="item">
    <div class="container">
        <div class="row">
            <div class="col2">
                <div class="block_image">
                    {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xlarge" crop="auto" alt=$item.user_name class="iloutline img_scale"}
                </div>
            </div>
            <div class="col10 last">
                <div style="padding:0 6px 0 12px;overflow-wrap:break-word">
                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                    <span class="normal"><small>{$item._created|jrCore_format_time}, by {$item.user_name}</small>
                    <br>{$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:120}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{/foreach}
{/if}

