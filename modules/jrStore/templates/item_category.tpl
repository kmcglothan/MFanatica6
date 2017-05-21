{jrCore_module_url module="jrStore" assign="murl"}

{if !isset($_post._2)}

    {* We're showing a list of existing products *}

    <div class="block">

        <div class="title">
            <div class="block_config">
                {jrCore_item_create_button module="jrStore" profile_id=$_profile_id}
            </div>
            <h1>{jrCore_lang module="jrStore" id="21" default="Category"}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; {jrCore_lang module="jrStore" id="21" default="Category"}
            </div>
        </div>

        {capture name="row_template" assign="template"}
            {literal}
                {if isset($_items) && is_array($_items)}
                {jrCore_module_url module="jrStore" assign="murl"}
                {foreach from=$_items item="item"}
                <div class="item">
                    <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category}</a></h3>
                </div>
                {/foreach}
                {/if}
            {/literal}
        {/capture}

        <div class="block_content">

            {jrCore_list module="jrStore" profile_id=$_profile_id order_by="_created desc" group_by="product_category_url" pagebreak="6" page=$_post.p template=$template pager=true}

        </div>

    </div>

{else}

    {* Show our video items in this album *}
    {capture name="row_template" assign="template"}
    {literal}

    {if isset($_items) && is_array($_items)}
    {jrCore_module_url module="jrStore" assign="murl"}
    <div class="block">

        <div class="title">
            <div class="block_config">
                {jrCore_item_create_button module="jrStore" profile_id=$_items.0._profile_id}
            </div>
            <h1>{$_items.0.product_category}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$_items.0.profile_url}/">{$_items.0.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$_items.0.profile_url}/{$murl}/category">{jrCore_lang module="jrStore" id="21" default="Category"}</a> &raquo; {$_items.0.product_category}
            </div>
        </div>

        <div class="block_content">

            {foreach $_items as $item}
            <div class="item">

                <div class="container">
                    <div class="row">
                        <div class="col2">
                            <div class="block_image">
                                {jrCore_module_function function="jrImage_display" module="jrStore" type=$item.product_image_primary item_id=$item._item_id size="small" class="iloutline" alt=$item.store_title crop="auto"}
                            </div>
                        </div>
                        <div class="col5">
                            <div class="p5">
                                <h3><a href="{jrProfile_item_url module="jrStore" profile_url=$item.profile_url item_id=$item._item_id title=$item.product_title}">{$item.product_title}</a></h3><br>
                                {jrCore_module_function function="jrRating_form" type="star" module="jrStore" index="1" item_id=$item._item_id current=$item.product_rating_1_average_count|default:0 votes=$item.product_rating_1_number|default:0}
                                {if isset($item.product_qty) && $item.product_qty > 0}
                                <br>
                                <span class="info_c">{jrCore_lang module="jrStore" id="42" default="Available Quantity"}: {$item.product_qty}</span>
                                {/if}
                            </div>
                        </div>
                        <div class="col5 last">
                            <div class="block_config">
                                {if $item.product_qty > 0}
                                    {$quantity_max = $item.product_qty}
                                    {else}
                                    {$quantity_max = 9999}
                                {/if}
                                {if isset($item.product_qty) && $item.product_qty === "0"}
                                   <span class="sold_out">{jrCore_lang module="jrStore" id="50" default="Sold Out"}</span>
                                {else}
                                    {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrStore" field="product" item=$item quantity_max=$quantity_max}
                                {/if}
                                {jrCore_module_function function="jrFoxyCartBundle_button" module="jrStore" field="product" item=$item}
                                {jrCore_item_list_buttons module="jrStore" field="product_file" item=$item}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {/foreach}

        </div>

    </div>
    {/if}

    {/literal}
    {/capture}

    {jrCore_list module="jrStore" profile_id=$_profile_id search2="product_category_url = `$_post._2`" order_by="_item_id asc" template=$template}


{/if}
