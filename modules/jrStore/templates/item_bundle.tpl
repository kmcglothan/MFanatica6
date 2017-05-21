
{if isset($bundle_item)}

    {jrCore_module_url module="jrStore" assign="murl"}
    <div class="item" id="jrStore{$bundle_item._item_id}">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.product_title_url}">{jrCore_module_function function="jrImage_display" module="jrStore" type="product_image" item_id=$bundle_item._item_id size="small" class="iloutline" alt=$bundle_item.product_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col6">
                    <div class="p5" style="overflow-wrap:break-word">
                        <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.product_title_url}">{$bundle_item.product_title}</a></h3><br>
                        <span class="info">{jrCore_lang module="jrStore" id="21" default="Category"}:</span> <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/category/{$bundle_item.product_category_url}"><span class="info_c">{$bundle_item.product_category}</span></a>
                    </div>
                </div>
                <div class="col4 last">
                    <div class="block_config">
                        {jrCore_module_function function="jrRating_form" type="star" module="jrStore" index="1" item_id=$bundle_item._item_id current=$bundle_item.product_rating_1_average_count|default:0 votes=$bundle_item.product_rating_1_number|default:0 }
                        {jrCore_module_function function="jrFoxyCartBundle_button" module="jrStore" field="product" item=$bundle_item}
                        {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrStore`$bundle_item._item_id`" module="jrStore" bundle_id=$bundle_id item=$bundle_item}
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>

    </div>
{/if}
