{jrCore_module_url module="jrBlog" assign="murl"}

{if isset($_post._1) && $_post._1 == 'category'}
{*this is the CATEGORY page, it doesnt show the index header so no breadcrumbs.  needs its own header.*}
<div class="block">
    <h1 style="text-transform:capitalize;">{$_items[0].blog_category}</h1>
   <div class="breadcrumbs">
        <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a> &raquo; {$_items[0].blog_category}
    </div>
</div>
{/if}


{if isset($_items)}
    {foreach from=$_items item="item"}
        <div class="block blogpost">
            <div style="float: right">
                <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}'">{jrCore_icon icon="gear"}</a>
            </div>

            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
            <br>
            {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
            <div class="normal p5">
                {$item.blog_text|truncate:400:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}
            </div>
            <hr>
            <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}"><span class="capital">{$item.blog_category}</span></a>
            {if jrCore_module_is_active('jrComment')}
                <span class="normal"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"} &raquo;</a></span>
            {/if}
            <hr>
        </div>
    {/foreach}
{/if}

<!-- the disqus comment count code -->
{jrDisqus_comment_count}

{if $info.total_pages > 1}
<div class="block">
    <table style="width:100%;">
        <tr>

            <td style="width:25%;">
                {if isset($info.prev_page) && $info.prev_page > 0}
                    <input type="button" value="{jrCore_lang module="jrCore" id=26 default="&lt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.prev_page}');">
                {/if}
            </td>

            <td style="width:50%;text-align:center;">
                {if $info.total_pages <= 5 || $info.total_pages > 500}
                    {$info.page} &nbsp;/ {$info.total_pages}
                    {else}
                    <form name="form" method="post" action="_self">
                        <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrCore_window_location('{$info.page_base_url}/p=' +sel);">
                            {for $pages=1 to $info.total_pages}
                                {if $info.page == $pages}
                                    <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                    {else}
                                    <option value="{$pages}"> {$pages}</option>
                                {/if}
                            {/for}
                        </select>&nbsp;/&nbsp;{$info.total_pages}
                    </form>
                {/if}
            </td>

            <td style="width:25%;text-align:right;">
                {if isset($info.next_page) && $info.next_page > 1}
                    <input type="button" value="{jrCore_lang module="jrCore" id=27 default="&gt;"}" class="form_button" onclick="jrCore_window_location('{$info.page_base_url}/p={$info.next_page}');">
                {/if}
            </td>

        </tr>
    </table>
</div>
{/if}
