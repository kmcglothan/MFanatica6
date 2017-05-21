{* default index for profile *}
{jrCore_module_url module="jrVideo" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrVideo" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrVideo" profile_id=$_profile_id}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrMaestro_sort template="icons.tpl" nav_mode="jrVideo" profile_url=$profile_url}
        <span>{jrCore_lang module="jrVideo" id="35" default="Video"} by {$profile_name}</span>
        <input type="hidden" id="murl" value="{$murl}"/>
        <input type="hidden" id="target" value="#list"/>
        <input type="hidden" id="pagebreak" value="12"/>
        <input type="hidden" id="mod" value="jrVideo"/>
        <input type="hidden" id="profile_id" value="{$_profile_id}"/>
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list
                    module="jrVideo"
                    profile_id=$_profile_id
                    order_by='video_display_order numerical_asc'
                    pagebreak=10
                    page=$_post.p
                    pager=true}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        <ul id="actions_tab">
            <li class="solo" id="channels_tab">
                <a href="#"></a>
            </li>
        </ul>
        <span>{jrCore_lang skin="jrMaestro" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrVideo"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=12
                    require_image="video_image"
                    template="chart_video.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>