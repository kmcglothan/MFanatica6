{jrCore_module_url module="jrBundle" assign="iburl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrBundle" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrBundle" id="1" default="Item Bundles"}</h1><br>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$iburl}">{jrCore_lang module="jrBundle" id="1" default="Item Bundles"}</a>
        </div>
    </div>

    <div class="block_content">

        {jrCore_list module="jrBundle" profile_id=$_profile_id order_by="bundle_display_order numerical_asc" pagebreak="4" page=$_post.p pager=true}

    </div>

</div>
