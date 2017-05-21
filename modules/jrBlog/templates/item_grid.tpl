{jrCore_module_url module="jrBlog" assign="murl"}

{if $_params.grid == 2 ||$_params.grid == 3 ||$_params.grid == 4 ||$_params.grid == 6}
    {$col = 12/$_params.grid}
    {$grid = $_params.grid}
{else}
    {$col = 4}
    {$grid = 3}
{/if}
{if isset($_items)}
    {foreach $_items as $item}

        {if $item@first || ($item@iteration % $grid) == 1}
            <div class="row">
        {/if}
        <div class="col{$col}">
            <div class="item">

                <div class="block_config">
                    {jrCore_item_list_buttons module="jrBlog" item=$item}
                </div>

                <div style="padding-left:5px">
                    <h2>
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a>
                    </h2>
                </div>

                <div class="">
                    {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                          {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="xlarge" alt=$item.blog_title width=false height=false class="iloutline img_scale"}
                    {/if}
                    <span class="normal">{jrCore_lang module="jrBlog" id="28" default="By"} {$item.user_name}, {$item.blog_publish_date|jrCore_format_time:false:"%F"}</span>
                    {$item.blog_text|jrBlog_readmore|jrCore_format_string:$item.profile_quota_id}
                </div>

                <div style="border-top:1px solid #DDD">
                    <div class="container">
                        <div class="row">
                            <div class="col12">
                                <div class="p5">
                                    <span class="info">{jrCore_lang module="jrBlog" id="26" default="Posted in"}: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.blog_category_url|default:"default"}">{$item.blog_category|default:"default"}</a></span>
                                    {if jrCore_module_is_active('jrComment')}
                                        <span class="info"> | <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comment_section"> {$item.blog_comment_count|default:0} {jrCore_lang module="jrBlog" id="27" default="comments"}</a></span>
                                    {/if}

                                  <span class="{if !jrCore_is_mobile_device()} float-right{/if}">
                                    {* check to see if the blog has a pagebreak in it *}
                                    {if strpos($item.blog_text,'<!-- pagebreak -->')}
                                        <span class="info"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">{jrCore_lang module="jrBlog" id="25" default="Read more"} &raquo;</a></span>
                                    {/if}
                                  </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        {if $item@last || ($item@iteration % $grid) == 0}
            </div>
        {/if}


    {/foreach}
{/if}