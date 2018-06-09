{jrCore_module_url module="jrBlog" assign="murl"}

{if isset($_post._1) && $_post._1 == 'category'}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

    <div class="page_nav clearfix">
        <div class="breadcrumbs">
            {jrCore_include template="profile_header_minimal.tpl"}
            {if strlen($_post._2) > 0}
                {jrBeatSlinger_breadcrumbs module="jrBlog" profile_url=$profile_url page="group" item=$_items[0]}
            {else}
                {jrBeatSlinger_breadcrumbs module="jrBlog" profile_url=$profile_url page="group"}
            {/if}
        </div>
        <div class="action_buttons">
            {jrCore_item_index_buttons module="jrBlog" profile_id=$_profile_id}
        </div>
    </div>

<div class="col8">
    <div class="main">
        <div class="box">
            {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrBlog" profile_url=$profile_url}
            <input type="hidden" id="murl" value="{$murl}"/>
            <input type="hidden" id="target" value="#list"/>
            <input type="hidden" id="pagebreak" value="8"/>
            <input type="hidden" id="mod" value="jrBlog"/>
            <input type="hidden" id="profile_id" value="{$_profile_id}"/>

            <div class="box_body">
                <div class="wrap">
                    <div class="row" id="list" style="overflow: visible;">

                        {/if}

                        {if isset($_items)}
                            {foreach $_items as $item}
                                <div class="list_item">
                                    <div class="wrap clearfix">
                                        <div class="row">
                                            {if strlen($item.blog_image_name) > 0}
                                                <div class="col4">
                                                    <div class="image">
                                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}">
                                                            {jrCore_module_function
                                                            function="jrImage_display"
                                                            module="jrBlog"
                                                            type="blog_image"
                                                            item_id=$item._item_id
                                                            size="xxxlarge"
                                                            crop="auto"
                                                            class="img_scale"
                                                            alt=$item.blog_title
                                                            width=false
                                                            height=false
                                                            }</a>
                                                    </div>
                                                </div>
                                                {$class = "col8"}
                                            {else}
                                                {$class = "col12"}
                                            {/if}

                                            <div class="{$class}">
                                                <div class="title">
                                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}"> {$item.blog_title|truncate:75}</a>
                                                </div>
                                                <span class="date">{$item.blog_publish_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p"}</span><br>
                                            <span>
                                                {$item.blog_text|strip_tags|truncate:250}
                                            </span>

                                                <div class="list_buttons">
                                                    {jrCore_item_list_buttons module="jrBlog" item=$item}
                                                </div>

                                                <div class="data clearfix">
                                                    <span>{$item.blog_comment_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="109" default="Comments"}</span>
                                                    <span>{$item.blog_like_count|jrCore_number_format} {jrCore_lang skin="jrBeatSlinger" id="110" default="Likes"}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            {/foreach}
                        {else}
                            {jrCore_include template="no_items.tpl"}
                        {/if}

                        {if isset($_post._1) && $_post._1 == 'category'}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="col4 last">
        <div class="box">
            <ul id="actions_tab">
                <li class="solo" id="categories_tab">
                    <a href="#"></a>
                </li>
            </ul>
            <span>{jrCore_lang skin="jrBeatSlinger" id="111" default="You May Also Like"}</span>

            <div class="box_body">
                <div class="wrap">
                    <div id="list" class="sidebar">
                        {jrCore_list
                        module="jrBlog"
                        profile_id=$_profile_id
                        order_by='_created RANDOM'
                        pagebreak=8
                        template="chart_blog.tpl"}
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
