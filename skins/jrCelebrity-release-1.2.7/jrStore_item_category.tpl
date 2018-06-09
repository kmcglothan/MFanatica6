{jrCore_module_url module="jrStore" assign="murl"}

{if !isset($_post._2)}
    {jrProfile_disable_header}
    {jrProfile_disable_sidebar}
    <div class="page_nav clearfix">
        <div class="breadcrumbs">
            {jrCore_include template="profile_header_minimal.tpl"}
            {jrCelebrity_breadcrumbs module="jrAudio" profile_url=$profile_url profile_name=$profile_name page="group"}
        </div>
        <div class="action_buttons">
            {jrCore_item_create_button module="jrStore" profile_id=$_profile_id}
        </div>
    </div>

    <div class="box">
        {jrCelebrity_sort template="icons.tpl" nav_mode="jrStore" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div id="list">
                    {capture name="row_template" assign="template"}
                    {literal}
                        {if isset($_items) && is_array($_items)}
                        {jrCore_module_url module="jrStore" assign="murl"}
                        {foreach from=$_items item="item"}
                        <div class="item">
                                        <span class="title">
                                            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/category/{$item.product_category_url}">{$item.product_category}</a>
                                        </span>
                        </div>
                        {/foreach}
                        {/if}
                    {/literal}
                    {/capture}

                    {jrCore_list module="jrStore" profile_id=$_profile_id order_by="_created desc" group_by="product_category_url" pagebreak="6" page=$_post.p template=$template pager=true}
                </div>
            </div>
        </div>
    </div>

{else}

    {* Show our product items in this album *}
    {capture name="row_template" assign="template"}
    {literal}
        {jrCore_page_title title="`$_items[0]['product_album']` - `$_items[0]['profile_name']` inside"}
        {jrCore_module_url module="jrStore" assign="murl"}
        {jrProfile_disable_header}
        {jrProfile_disable_sidebar}

        <div class="page_nav clearfix">
            <div class="breadcrumbs">
                {jrCore_include template="profile_header_minimal.tpl"}
                {jrCelebrity_breadcrumbs module="jrStore" profile_url=$_items[0].profile_url profile_name=$_items[0].profile_name page="group" item=$_items[0]}
            </div>
            <div class="action_buttons">
                {jrCore_item_create_button module="jrStore" profile_id=$_items.0._profile_id}
            </div>
        </div>
        <div class="col8">
            <div class="box">
                {jrCelebrity_sort template="icons.tpl" nav_mode="jrStore" profile_url=$_items[0].profile_url}
                <div class="box_body">
                    <div class="wrap">
                        <div id="list">
                            {foreach $_items as $item}
                            <div class="list_item">
                                <div class="wrap clearfix">
                                    <div class="col4">
                                        <div class="image">
                                            {if isset($item._product_images)}
                                            {foreach $item._product_images as $img}
                                            {if $img@first}
                                            <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="{$item.product_title_url}" title="{$item.store_title|jrCore_entity_string}">
                                                {jrCore_module_function
                                                function="jrImage_display"
                                                module="jrStore"
                                                type=$img
                                                item_id=$item._item_id
                                                size="xlarge"
                                                crop="auto"
                                                class="img_scale"
                                                alt=$item.store_title
                                                width=false
                                                height=false}</a>
                                            {else}
                                            <a href="{$jamroom_url}/{$murl}/image/{$img}/{$item._item_id}/1280" data-lightbox="{$item.product_title_url}" title="{$item.store_title|jrCore_entity_string}"></a>
                                            {/if}
                                            {/foreach}
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="col8">
                                        <span class="title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.product_title_url}">{$item.product_title|truncate:55}</a></span>

                                        {if isset($item.product_qty) && $item.product_qty > 0}
                                        <span class="date">{jrCore_lang module="jrStore" id="42" default="Quantity Available"}: {$item.product_qty}</span>
                                        {/if}<br>
                                        <div class="media_text">
                                            {$item.product_body|strip_tags|truncate:200}
                                        </div>
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
                                            <span>{$item.product_comment_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="109" default="Comments"}</span>
                                            <span>{$item.product_like_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="110" default="Likes"}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col4 last">
            <div class="box">
                <ul id="actions_tab">
                    <li class="solo" id="album_tab">
                        <a href="#"></a>
                    </li>
                </ul>
                <span>{jrCore_lang skin="jrCelebrity" id="111" default="You May Also Like"}</span>
                <div class="box_body">
                    <div class="wrap">
                        <div id="list" class="sidebar">
                            {jrCore_list
                            module="jrStore"
                            search="_profile_id != `$_items[0]._profile_id`"
                            order_by='_created RANDOM'
                            pagebreak=10
                            template="chart_product.tpl"}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/literal}
    {/capture}

    {jrCore_list module="jrStore" profile_id=$_profile_id search2="product_category_url = `$_post._2`" order_by="_item_id asc" template=$template}

{/if}
