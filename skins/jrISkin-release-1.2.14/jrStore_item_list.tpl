{jrCore_module_url module="jrStore" assign="murl"}
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
                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{jrCore_module_function function="jrImage_display" module="jrStore" type=$img_type item_id=$item._item_id size="xlarge" class="iloutline img_scale" crop="auto" alt=$item.product_title width=false height=false}</a>
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
                            <span class="date">{jrCore_lang module="jrStore" id="42" default="Available Quantity"}: {$item.product_qty}</span>
                        {elseif isset($item.product_qty) && $item.product_qty === "0"}
                            <span class="date sold_out">{jrCore_lang module="jrStore" id="50" default="Sold Out"}</span>
                        {/if}
                        <br>
                        <span>{$item.product_body|strip_tags|truncate:200}</span>
                        <div class="list_buttons">
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
                        <div class="data clearfix">
                            <span>{$item.product_comment_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="109" default="Comments"}</span>
                            <span>{$item.product_like_count|jrCore_number_format} {jrCore_lang skin="jrISkin" id="110" default="Likes"}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    {/foreach}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}