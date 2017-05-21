{jrCore_module_url module="jrBlog" assign="murl"}
{if isset($_post._1) && $_post._1 == 'category'}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_create_button module="jrBlog" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrBlog" id="20" default="Category"}: {$_items[0].blog_category|default:"default"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrBlog" id="24" default="Blog"}</a> &raquo; {$_items[0].blog_category|default:"default"}
        </div>
    </div>

    <div class="block_content">
{/if}

        {if isset($_items)}
            {foreach $_items as $item}
                <div class="item">

                    <div class="block_config">
                        {jrCore_item_list_buttons module="jrBlog" item=$item}
                    </div>

                    <div style="padding-left:5px">
                        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h2>
                        <br>
                        <span class="normal">{jrCore_lang module="jrBlog" id="28" default="By"} {$item.user_name}, {$item.blog_publish_date|jrCore_format_time:false:"%F"}</span>
                    </div>

                    <div class="p20 pt10">
                        {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                            <div class="float-right" style="margin-top:12px;">
                                {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="icon" alt=$item.blog_title width=false height=false class="iloutline img_shadow" style="margin-left:12px;margin-bottom:12px;"}
                            </div>
                        {/if}
                        {$item.blog_text|jrBlog_readmore|jrCore_format_string:$item.profile_quota_id}
                    </div>
                    <div class="clear"></div>

                    <div style="border-top:1px solid #DDD">
                        <div class="container">
                            <div class="row">
                                <div class="col6">
                                    <div class="p5">
                                        <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url|default:"default"}">{$item.blog_category|default:"default"}</a></span>
                                        {if jrCore_module_is_active('jrComment')}
                                            <span class="info"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comment_section"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"}</a></span>
                                        {/if}
                                    </div>
                                </div>
                                <div class="col6 last">
                                    <div class="p5{if !jrCore_is_mobile_device()} right{/if}">
                                        {* check to see if the blog has a pagebreak in it *}
                                        {if strpos($item.blog_text,'<!-- pagebreak -->')}
                                            <span class="info"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}{if $_conf.jrBlog_pagination == 'off'}/#page2{/if}">{jrCore_lang module="jrBlog" id="25" default="Read more"} &raquo;</a></span>
                                        {/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            {/foreach}
        {/if}

{if isset($_post._1) && $_post._1 == 'category'}
    </div>

</div>
{/if}
