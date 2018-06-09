{* default index for profile *}
{jrCore_module_url module="jrProduct" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrProduct" profile_url=$profile_url profile_name=$profile_name page="index" single=true}
    </div>
    <div class="action_buttons">
        {jrCore_item_order_button module="jrProduct" profile_id=$_profile_id icon="refresh"}
        {jrCore_item_create_button module="jrProduct" profile_id=$_profile_id}
    </div>
</div>

<div id="product_tabs"></div>

<div class="col8">
    <div class="box">
        {jrMaestro_sort template="icons.tpl" nav_mode="jrProduct" profile_url=$profile_url}
        <span>{jrCore_lang module="jrProduct" id="19" default="Products"} by {$profile_name}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {jrCore_list module="jrProduct"  search2="_profile_id = `$_profile_id`" order_by="product_display_order numerical_asc" pagebreak=6 page=$_post.p pager=true}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col4 last">
    <div class="box">
        {jrMaestro_sort template="icons.tpl" nav_mode="jrProduct" profile_url=$profile_url single=true}
        <span>{jrCore_lang skin="jrMaestro" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list module="jrProduct" search="_profile_id != `$_profile_id`" order_by='_created RANDOM' pagebreak=12 require_image="product_image" template="chart_product.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>
