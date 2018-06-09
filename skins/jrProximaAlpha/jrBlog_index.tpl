{jrCore_page_title title="Blog"}
{jrCore_include template="header.tpl" show_main_box=1}


{* This is the embedded template that is shown for EACH blog entry *}
{capture name="template1" assign="blog_tpl"}
{literal}
{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="row">
        <div class="col2">
            <div class="p10">
                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="medium" crop="portrait" class="img_scale img-rounded" alt=$item.user_name width="128" height="128"}
            </div>
        </div>
        <div class="col10 last">
            <div class="p10">
                {if $_conf.jrProximaCore_enable_profiles == 'on'}
                <h1 class="blog-title"><a href="{$jamroom_url}/blog/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h1>
                {else}
                <h1 class="blog-title">{$item.blog_title}</h1>
                {/if}
                <br><span class="blog-byline">{jrCore_lang module="jrBlog" id="28" default="By"} {$item.user_name}, {$item.blog_publish_date|jrCore_format_time:false:"%F"}</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col12 last">
            <div class="normal blog-text">
                {$item.blog_text|jrCore_format_string:$item.profile_quota_id}
            </div>
        </div>
    </div>
    {/foreach}
{/if}
{/literal}
{/capture}


{* This is the embedded template that is shown for EACH archive entry *}
{capture name="template2" assign="archive_tpl"}
{literal}
{if isset($_items)}
    {foreach from=$_items item="item"}
        {if $_conf.jrProximaCore_enable_profiles == 'on'}
        <h3 class="blog-archive-title"><a href="{$jamroom_url}/blog/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3><br>
        {else}
        <h3 class="blog-archive-title">{$item.blog_title}</h3><br>
        {/if}
    {/foreach}
{/if}
{/literal}
{/capture}


<div class="container">
    <div class="row">
        <div class="col9">
            <div class="p10">
                {if jrCore_checktype($_post.option, 'number_nz')}
                    {jrCore_list module="jrBlog" search1="_item_id = `$_post.option`" template=$blog_tpl}
                {else}
                    {jrCore_list module="jrBlog" search1="_profile_id in `$_conf.jrProximaAlpha_blog_profile_ids`" order_by="blog_publish_date numerical_desc" pagebreak="3" template=$blog_tpl page=$_post.p pager=true}
                {/if}
            </div>
        </div>
        <div class="col3 last">
            <div class="p20">
                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="10" default="Older Posts"}</h2><br>
                {jrCore_list module="jrBlog" search1="_profile_id in `$_conf.jrProximaAlpha_blog_profile_ids`" order_by="blog_publish_date numerical_desc" limit="100" template=$archive_tpl}
            </div>
        </div>
    </div>
</div>

{jrCore_include template="footer.tpl" show_main_box=1}