
{jrCore_module_url module="jrProduct" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMSkin_breadcrumbs module="jrProduct" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {if isset($item.product_qty) && $item.product_qty === "0"}
            <span class="sold_out">{jrCore_lang module="jrProduct" id="50" default="Sold Out"}</span>
        {else}
            {if $item.product_qty > 0}
                {$quantity_max = $item.product_qty}
            {else}
                {$quantity_max = 9999}
            {/if}
            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrProduct" field="product" item=$item quantity_max=$quantity_max}
        {/if}
        {jrCore_module_function function="jrFoxyCartBundle_button" module="jrProduct" field="product" item=$item}

        {jrCore_item_detail_buttons module="jrProduct" field="product_file" item=$item}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrMSkin_sort template="icons.tpl" nav_mode="jrProduct" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div class="wrap clearfix">
                        {if strlen($item.product_image_size) > 0}
                            <div class="media_image">
                                {if isset($item._product_images)}
                                    {foreach $item._product_images as $img}
                                        {if $img@first}
                                            <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.store_title|jrCore_entity_string}">
                                                {jrCore_module_function
                                                function="jrImage_display"
                                                module="jrProduct"
                                                type=$img
                                                item_id=$item._item_id
                                                size="xlarge"
                                                class="img_scale"
                                                alt=$item.store_title
                                                width=false
                                                height=false
                                                }
                                            </a>
                                        {else}
                                            <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="images" title="{$item.store_title|jrCore_entity_string}"></a>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </div>
                        {/if}
                        <span class="title">{$item.product_title|truncate:60}</span>

                        <span class="location"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category|jrCore_strip_html|truncate:60}</a> </span>

                        <div>
                            <p> {$item.product_description} </p>
                        </div>

                        {if isset($item.product_qty) && $item.product_qty > 0}
                            <span class="info_c">{jrCore_lang module="jrProduct" id="12" default="Quantity Available"}: {$item.product_qty}</span>
                        {/if}
                        &bull; <span class="info_c">{jrCore_lang module="jrProduct" id="9" default="click to view images"} ({$item.product_image_count})</span>
                        <br><br>{jrCore_module_function function="jrRating_form" type="star" module="jrProduct" index="1" item_id=$item._item_id current=$item.product_rating_1_average_count|default:0 votes=$item.product_rating_1_number|default:0}
                    </div>
                </div>
                {* bring in module features *}
                <div class="action_feedback">
                    {jrMSkin_feedback_buttons module="jrProduct" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrProduct_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrProduct"
                            index="1"
                            item_id=$item._item_id
                            current=$item.product_rating_1_average_count|default:0
                            votes=$item.product_rating_1_number|default:0}</div>
                    {/if}
                    {jrCore_item_detail_features module="jrProduct" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrMSkin_sort template="icons.tpl" nav_mode="jrProduct" profile_url=$profile_url single=true}
        <span>{jrCore_lang skin="jrMSkin" id="111" default="You May Also Like"}</span>
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrProduct"
                    profile_id=$item.profile_id
                    order_by='_created RANDOM'
                    pagebreak=8
                    template="chart_product.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>





