{jrCore_module_url module="jrProduct" assign="murl"}

<div class="block">
    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrProduct" profile_id=$_profile_id}
        </div>
        <h1>{$_cat.cat_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrProduct" id=19 default="Products"}</a> &raquo; {$_cat.cat_title}
        </div>
    </div>
    <div class="block_content">
        <div id="default_list">
            {jrCore_list module="jrProduct" search1="product_category_url = `$_cat.cat_title_url`" search2="_profile_id = `$_profile_id`" order_by="product_display_order numerical_asc" pagebreak=6 page=$_post.p pager=true}
        </div>
    </div>
</div>
