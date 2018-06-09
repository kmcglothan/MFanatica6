{* default index for profile *}
{jrCore_module_url module="jrAudio" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrCelebrity_breadcrumbs module="jrAudio" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrAudio" profile_id=$_profile_id}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}

        <div class="box_body">
            <div class="wrap">
                <div id="list" class="main">
                    {jrCore_list
                    module="jrAudio"
                    profile_id=$_profile_id
                    order_by='audio_display_order numerical_asc'
                    pagebreak=8
                    page=$_post.p
                    pager=true}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrAudio" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrAudio"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=12
                    require_image="audio_image"
                    template="chart_audio.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>