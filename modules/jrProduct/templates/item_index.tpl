{jrCore_module_url module="jrProduct" assign="murl"}

<div class="block">
    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrProduct" profile_id=$_profile_id}
        </div>
        <h1>{if isset($_post._1) && strlen($_post._1) > 0}{$_post._1}{else}{jrCore_lang module="jrProduct" id="19" default="Products"}{/if}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{if isset($_post._1) && strlen($_post._1) > 0}{$_post._1}{else}{jrCore_lang module="jrProduct" id="19" default="Products"}{/if}</a>
        </div>
    </div>
    <div class="block_content">
        <div id="default_list">
            {jrCore_list module="jrProduct" profile_id=$_profile_id order_by="product_display_order numerical_asc" pagebreak="6" page=$_post.p pager=true}
        </div>
    </div>
</div>
