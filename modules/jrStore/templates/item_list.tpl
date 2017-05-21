{jrCore_module_url module="jrStore" assign="murl"}
{if isset($_items)}

    {foreach $_items as $item}
        <div class="item">

            <div class="container">
                <div class="row">
                    <div class="col2">
                        <div class="block_image">
                            {if strlen($item._product_images.0)}
                                {$img_type = $item._product_images.0}
                            {else}
                                {$img_type = "product_image"}
                            {/if}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{jrCore_module_function function="jrImage_display" module="jrStore" type=$img_type item_id=$item._item_id size="xlarge" class="iloutline img_scale" crop="auto" alt=$item.product_title width=false height=false}</a>
                        </div>
                    </div>
                    <div class="col6">
                        <div class="p5" style="overflow-wrap:break-word;padding-left:12px;">
                            <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{$item.product_title}</a></h2><br>
                            <span class="info">Category:</span> <span class="info_c"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category}</a></span>
                            {if isset($item.product_qty) && $item.product_qty > 0}
                                <br>
                                <span class="info_c">{jrCore_lang module="jrStore" id="42" default="Available Quantity"}: {$item.product_qty}</span>
                            {elseif isset($item.product_qty) && $item.product_qty === "0"}
                                <br>
                                <span class="info_c sold_out">{jrCore_lang module="jrStore" id="50" default="Sold Out"}</span>
                            {/if}
                        </div>
                    </div>
                    <div class="col4 last">
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

                            {jrCore_item_list_buttons module="jrStore" field="product_file" item=$item}

                        </div>
                        <div class="mt10" style="float:right">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrStore" index="1" item_id=$item._item_id current=$item.product_rating_1_average_count|default:0 votes=$item.product_rating_1_count|default:0}
                        </div>
                        <div class="clear"></div>
                    </div>
                </div>

            </div>

        </div>
    {/foreach}

{/if}