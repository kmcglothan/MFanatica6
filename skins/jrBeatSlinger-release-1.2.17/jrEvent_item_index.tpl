{* default index for profile *}
{jrCore_module_url module="jrEvent" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrEvent" profile_url=$profile_url profile_name=$profile_name page="index"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrEvent" profile_id=$_profile_id}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrEvent" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrEvent" profile_id=$_profile_id order_by="event_date asc" pagebreak="10" page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
    <style>
        table.page_content {
            display: none;
        }
    </style>
</div>

<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrEvent" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrBeatSlinger" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrEvent"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=8
                    require_image="event_image"
                    template="chart_event.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>