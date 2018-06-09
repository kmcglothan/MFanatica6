{jrCore_module_url module="jrProduct" assign="murl"}
{if isset($_items)}
    {foreach $_items as $item}

        <div class="list_item">
            <div class="wrap clearfix">
                <div class="row">
                    <div class="col4">
                        <div class="image">
                            {if strlen($item._product_images.0)}
                                {$img_type = $item._product_images.0}
                            {else}
                                {$img_type = "product_image"}
                            {/if}
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{jrCore_module_function function="jrImage_display" module="jrProduct" type=$img_type item_id=$item._item_id size="xlarge" class="iloutline img_scale" crop="auto" alt=$item.product_title width=false height=false}</a>
                        </div>
                    </div>
                    <div class="col8">
                        {if $item.product_active == 'on' && $item.product_file_extension == 'mp3'}
                            {jrCore_media_player type="jrAudio_button" module="jrAudio" field="product_file" item=$item}
                        {else}
                            &nbsp;
                        {/if}

                        <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{$item.product_title}</a></span>
                        <span class="date"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/albums/{$item.product_album_url}">{$item.product_album}</a></span>
                        <span class="date">{$item.product_genre}</span>

                        {if isset($item.product_qty) && $item.product_qty > 0}
                            <span class="date">{$item.product_qty} {jrCore_lang module="jrProduct" id="12" default="Available Quantity"}</span>
                        {elseif isset($item.product_qty) && $item.product_qty === "0"}
                            <span class="date sold_out">{jrCore_lang module="jrProduct" id="41" default="Sold Out"}</span>
                        {/if}
                        <br>
                        <span>{$item.product_description|strip_tags|truncate:200}</span>
                        <div class="list_buttons">
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
                        <div class="data clearfix">
                            <span>{$item.product_comment_count|jrCore_number_format} {jrCore_lang skin="jrMaestro" id="109" default="Comments"}</span>
                            <span>{$item.product_like_count|jrCore_number_format} {jrCore_lang skin="jrMaestro" id="110" default="Likes"}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}