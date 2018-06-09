{jrCore_module_url module="jrProduct" assign="murl"}
<div class="block">
    <div class="title">
        <div class="block_config">

            {if isset($item.product_qty) && is_numeric($item.product_qty) && $item.product_qty <= 0}
                <div class="cart-section">
                    <span class="cart-price">{jrCore_lang module="jrProduct" id=41 default="Sold Out"}</span>
                    {jrCore_icon icon="cancel"}
                </div>
            {else}
                {if $item.product_qty > 0}
                    {$quantity_max = $item.product_qty}
                    {else}
                    {$quantity_max = 9999}
                {/if}
                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrProduct" field="product" item=$item quantity_max=$quantity_max}
            {/if}

            {jrCore_module_function function="jrFoxyCartBundle_button" module="jrProduct" field="product" item=$item}

            {* Are we sold out *}
            {if isset($item.product_qty) && is_numeric($item.product_qty) && $item.product_qty <= 0}
                {jrCore_item_detail_buttons module="jrProduct" field="product" item=$item exclude="jrPayment_item_cart_button"}
            {else}
                {jrCore_item_detail_buttons module="jrProduct" field="product" item=$item}
            {/if}

        </div>
        <h1>{$item.product_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrProduct" id=19 default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category}</a> &raquo; {$item.product_title}
        </div>
    </div>
    <div class="block_content">
        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="block_image center">
                            {if isset($item._product_images)}
                                {foreach $item._product_images as $img}
                                    {if $img@first}
                                        <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.product_title|jrCore_entity_string}">{jrCore_module_function function="jrImage_display" module="jrProduct" type=$img item_id=$item._item_id size="large" class="iloutline" alt=$item.product_title width=164}</a>
                                    {else}
                                        <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.product_title|jrCore_entity_string}"></a>
                                    {/if}
                                {/foreach}
                                <br><span class="info_c">{jrCore_lang module="jrProduct" id=42 default="click to view images"} ({$item.product_image_count})</span>
                                <br>{jrCore_module_function function="jrRating_form" type="star" module="jrProduct" index="1" item_id=$item._item_id current=$item.product_rating_1_average_count|default:0 votes=$item.product_rating_1_number|default:0}
                                {if isset($item.product_qty) && $item.product_qty > 0}
                                <br><br><span class="info_c">{jrCore_lang module="jrProduct" id=12 default="Quantity Available"}: {$item.product_qty}</span>
                                {/if}
                            {/if}
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="normal p5">

                            {$item.product_description|jrCore_format_string:$item.profile_quota_id}

                            {if isset($item._product_cat_fields) && is_array($item._product_cat_fields) && count($item._product_cat_fields) > 0}

                                <br>
                                {foreach $item._product_cat_fields as $_cat_field}
                                    {if strlen($_cat_field.value) > 0}
                                        <strong>{$_cat_field.label}:</strong> {$_cat_field.value}<br>
                                    {/if}
                                {/foreach}

                                {if $item.product_item_shipping > 0}
                                    <strong>{jrCore_lang module="jrProduct" id=14 default="Shipping and Handling"}:</strong> {jrPayment_get_currency_symbol}{$item.product_item_shipping}
                                {/if}

                            {/if}

                        </div>
                    </div>
                </div>
            </div>
        </div>
        {* bring in module features *}
        {jrCore_item_detail_features module="jrProduct" item=$item}
    </div>
</div>
