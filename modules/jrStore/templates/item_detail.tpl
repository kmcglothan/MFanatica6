{jrCore_module_url module="jrStore" assign="murl"}

<div class="block">

    <div class="title">
        <div class="block_config">
            {if isset($item.product_qty) && $item.product_qty === "0"}
                <span class="sold_out">{jrCore_lang module="jrStore" id="50" default="Sold Out"}</span>
            {else}
                {if $item.product_qty > 0}
                    {$quantity_max = $item.product_qty}
                    {else}
                    {$quantity_max = 9999}
                {/if}
                {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrStore" field="product" item=$item quantity_max=$quantity_max}
            {/if}
            {jrCore_module_function function="jrFoxyCartBundle_button" module="jrStore" field="product" item=$item}

            {jrCore_item_detail_buttons module="jrStore" field="product_file" item=$item}

        </div>
        <h1>{$item.product_title}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrStore" id="19" default="Products"}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category">category</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category}</a> &raquo; {$item.product_title}
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
                                        <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.store_title|jrCore_entity_string}">{jrCore_module_function function="jrImage_display" module="jrStore" type=$img item_id=$item._item_id size="icon" class="iloutline" alt=$item.store_title width=false height=false}</a>
                                    {else}
                                        <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.store_title|jrCore_entity_string}"></a>
                                    {/if}
                                {/foreach}
                                <br><span class="info_c">{jrCore_lang module="jrStore" id="29" default="click to view images"} ({$item.product_image_count})</span>

                                <br>{jrCore_module_function function="jrRating_form" type="star" module="jrStore" index="1" item_id=$item._item_id current=$item.product_rating_1_average_count|default:0 votes=$item.product_rating_1_number|default:0}
                                {if isset($item.product_qty) && $item.product_qty > 0}
                                <br><br><span class="info_c">{jrCore_lang module="jrStore" id="42" default="Quantity Available"}: {$item.product_qty}</span>
                                {/if}
                            {/if}
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="normal p5">

                            {$item.product_body|jrCore_format_string:$item.profile_quota_id}

                        </div>
                    </div>
                </div>
            </div>

        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrStore" item=$item}

    </div>


</div>
