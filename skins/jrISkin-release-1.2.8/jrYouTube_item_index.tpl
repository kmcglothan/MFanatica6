{* default index for profile *}
{jrCore_module_url module="jrYouTube" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrYouTube" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrYouTube" profile_id=$_profile_id}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrYouTube" profile_url=$profile_url}
        <input type="hidden" id="murl" value="{$murl}"/>
        <input type="hidden" id="target" value="#list"/>
        <input type="hidden" id="pagebreak" value="10"/>
        <input type="hidden" id="mod" value="jrYouTube"/>
        <input type="hidden" id="profile_id" value="{$_profile_id}"/>
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {if strlen($_post.category) > 0}
                        {jrCore_list module="jrYouTube"
                        search="youtube_category_url = `$_post.category`"
                        profile_id=$_profile_id
                        order_by="youtube_display_order numerical_asc"
                        pagebreak="10"
                        page=$_post.p
                        pager=true}
                    {else}
                        {jrCore_list module="jrYouTube"
                        profile_id=$_profile_id
                        order_by="youtube_display_order numerical_asc"
                        pagebreak="10"
                        page=$_post.p
                        pager=true}
                    {/if}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrYouTube" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrISkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrYouTube"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=12
                    template="chart_youtube.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>