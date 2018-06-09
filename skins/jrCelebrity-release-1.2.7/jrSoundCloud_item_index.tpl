{* default index for profile *}
{jrCore_module_url module="jrSoundCloud" assign="murl"}

{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrSoundCloud" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrSoundCloud" profile_id=$_profile_id}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrSoundCloud" profile_url=$profile_url}
        <input type="hidden" id="murl" value="{$murl}" />
        <input type="hidden" id="target" value="#list" />
        <input type="hidden" id="pagebreak" value="20" />
        <input type="hidden" id="mod" value="jrSoundCloud" />
        <input type="hidden" id="profile_id" value="{$_profile_id}" />
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrSoundCloud" profile_id=$_profile_id order_by="soundcloud_display_order numerical_asc" pagebreak="20" page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        <ul id="actions_tab">
            <li class="solo" id="soundcloud_tab">
                <a href="#"></a>
            </li>
        </ul>
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrSoundCloud"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=12
                    require_image="audio_image"
                    template="chart_soundcloud.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>