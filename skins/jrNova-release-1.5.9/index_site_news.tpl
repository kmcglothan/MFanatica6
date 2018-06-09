{if isset($_items)}
    {jrCore_module_url module="jrBlog" assign="murl"}
    <div class="inner leader blogpost mb8 mr8">
        <div class="float-right">
            <a href="{$jamroom_url}/blogs/category/site-news"><span class="normal">{jrCore_lang  skin=$_conf.jrCore_active_skin id="52" default="Archives"}&nbsp;&raquo;</span></a>&nbsp;
        </div>
        <span class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="8" default="Site"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="9" default="News"}</span>
    </div>
    <div class="inner blogpost mb8 mr8" style="height:450px;overflow:auto;">
        <div class="p3-5" style="height:386px;overflow:auto;">
            {foreach from=$_items item="item"}
                <div style="float: right">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_icon icon="gear" size="24"}</a>
                </div>

                {jrCore_image module="jrBlog" image="date_icon.png" alt="published" style="padding-right: 4px;padding-top: 2px;vertical-align: middle;width: 20px;"}<span class="normal">{$item.blog_publish_date|jrCore_format_time}</span><br>
                <h2><a href="{$jamroom_url}/blogs/search/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2><br>
                <div class="normal p5">
                    {$item.blog_text|jrCore_format_string:$item.profile_quota_id|jrBlog_readmore|nl2br}
                </div>
                <hr>
                <span class="media_title">{jrCore_lang module="jrBlog" id="26" default="Posted in"}:</span> <span class="normal capital"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url}">{$item.blog_category}</a></span>
                {if jrCore_module_is_active('jrComment')}
                    <span class="normal capital"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"} &raquo;</a></span><br>
                {/if}
                <br>
            {/foreach}
        </div>
    </div>
    <!-- the disqus comment count code -->
    {jrDisqus_comment_count}
{/if}
