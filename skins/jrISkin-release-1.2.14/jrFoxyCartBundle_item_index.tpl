{* default index for profile *}
{jrCore_module_url module="jrFoxyCartBundle" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrFoxyCartBundle" profile_url=$profile_url profile_name=$profile_name page="detail"}
    </div>
    <div class="action_buttons">
        {jrCore_item_index_buttons module="jrFoxyCartBundle" profile_id=$_profile_id}
    </div>
</div>


<div class="col8">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrFoxyCartBundle" profile_url=$profile_url}
        <span>{jrCore_lang module="jrFoxyCartBundle" id="1" default="Item Bundles"} by {$profile_name}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrFoxyCartBundle" profile_id=$_profile_id order_by="_created desc" pagebreak="4" page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrISkin_sort template="icons.tpl" nav_mode="jrFoxyCartBundle" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrISkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrFoxyCartBundle"
                    search="_profile_id != `$_profile_id`"
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_bundle.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
