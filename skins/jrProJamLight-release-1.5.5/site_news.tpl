<div style="height:588px;overflow:auto;">
    {* ROW TEMPLATE *}
    {capture name="row_template" assign="site_news_template"}
        {literal}
            {if isset($_items)}
            {jrCore_module_url module="jrBlog" assign="murl"}
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
                    {if jrCore_module_is_active('jrComment')}
                    <div class="blog-div">
                <span class="blog-replies">
                    {if $item.profile_id == '1'}
                        <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    {else}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    {/if}
                </span>
                    </div>
                    {/if}
                    {if jrUser_is_master()}
                    <div class="float-right">
                        <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}'">{jrCore_icon icon="gear"}</a>
                    </div>
                    {/if}
                    <div class="clear"></div>
                </div>
                <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
                <div class="blog-text">
                    {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                    {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width="128" height="128" crop="auto" class="iloutline img_shadow" style="float:left;margin-right:15px;margin_bottom:15px;"}
                    {/if}
                    {$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrBlog_readmore|nl2br}
                </div>
            </div>
            {/foreach}
            {if $info.total_pages > 1}
            <div class="block">
                <table style="width:100%;">
                    <tr>

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.prev_page) && $info.prev_page > 0}
                            <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');"><span class="button-arrow-previous">&nbsp;</span></a>
                            {else}
                            <span class="button-arrow-previous-off">&nbsp;</span>
                            {/if}
                        </td>

                        <td class="body_5" style="width:50%;text-align:center;">
                            {if $info.total_pages <= 5 || $info.total_pages > 500}
                            {$info.page} &nbsp;/ {$info.total_pages}
                            {else}
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#site_news_div','{$jamroom_url}/site_news/p=' +sel);$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');">
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

                        <td class="body_5 page" style="width:25%;text-align:center;">
                            {if isset($info.next_page) && $info.next_page > 1}
                            <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');"><span class="button-arrow-next">&nbsp;</span></a>
                            {else}
                            <span class="button-arrow-next-off">&nbsp;</span>
                            {/if}
                        </td>

                    </tr>
                </table>
            </div>
            {/if}
            {/if}
        {/literal}
    {/capture}

    {* EVENT LIST FUNCTION *}
    {if isset($_conf.jrProJamLight_news_profile) && $_conf.jrProJamLight_news_profile > 0}
        {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id in `$_conf.jrProJamLight_news_profile`" search2="blog_category = news" template=$site_news_template pagebreak=$_conf.jrProJamLight_index_news_limit page=$_post.p}
    {else}
        {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = 1" search2="blog_category = news" template=$site_news_template pagebreak=$_conf.jrProJamLight_index_news_limit page=$_post.p}
    {/if}
</div>
