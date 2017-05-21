{jrCore_module_url module="jrFAQ" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrFAQ" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrFAQ" id="10" default="FAQ"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrFAQ" id="10" default="FAQ"}</a>
        </div>
    </div>

<div class="block_content">

{jrCore_list module="jrFAQ" profile_id=$_profile_id order_by="faq_display_order numerical_asc" group_by="faq_category_url" pagebreak="10" page=$_post.p pager=true}

</div>

</div>
