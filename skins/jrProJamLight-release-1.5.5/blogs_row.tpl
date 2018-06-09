{jrCore_module_url module="jrBlog" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
        <h3 style="font-weight:normal;">
            {if $item.profile_id == '1'}
                <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{if strlen($item.blog_title) > 35}{$item.blog_title|truncate:35:"...":false}{else}{$item.blog_title}{/if}</a>
            {else}
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{if strlen($item.blog_title) > 35}{$item.blog_title|truncate:35:"...":false}{else}{$item.blog_title}{/if}</a>
            {/if}
        </h3>

        <div style="font-size:12px;">{$item.blog_publish_date|jrCore_format_time}</div>
        <div style="font-size:11px;">
            <span class="highlight-txt"><i>{jrCore_lang skin=$_conf.jrCore_active_skin id="112" default="By"}:</i></span>&nbsp;<span class="capital">{$item.profile_name}</span>&nbsp;&nbsp;<span class="highlight-txt"><i>{jrCore_lang skin=$_conf.jrCore_active_skin id="129" default="Tag"}:</i></span>&nbsp;<span class="capital">{$item.blog_category}</span><br>
            {if jrCore_module_is_active('jrComment')}
                <br>
                <div class="float-right" style="padding-right:5px;">
                    {if $item.profile_id == '1'}
                        <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    {else}
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                    {/if}
                </div>
                <div class="clear"></div>
            {/if}
        </div>
    </div>
    {/foreach}
{/if}
