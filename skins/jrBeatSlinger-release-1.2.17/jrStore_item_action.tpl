{jrCore_module_url module="jrStore" assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$item._user_id
        size="icon"
        crop="auto"
        alt=$item.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}"
                                          title="{$item.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>

        {if $item.action_mode == 'create'}
            <span class="action_desc"><a
                        href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.product_title_url}">
                    {jrCore_lang module="jrStore" id="18" default="Created a new Product"}
                    .</a></span>
            <br>
        {else}
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.product_title_url}"
               title="{$item.product_title|jrCore_entity_string}">
                {jrCore_lang module="jrStore" id="126" default="Updated a Product"}.</a>
            <br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="item_media">
    <div class="wrap clearfix">
        {if strlen($item.action_data.product_image_size) > 0}
            <div class="media_image">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.product_title_url}"
                   title="{$item.action_data.product_title|jrCore_entity_string}">
                    {jrCore_module_function
                    function="jrImage_display"
                    module="jrStore"
                    type="product_image"
                    item_id=$item.action_item_id
                    size="xlarge"
                    class="img_scale"
                    alt=$item.action_data.product_title
                    }
                </a>
            </div>
        {/if}
        <span class="title">{$item.action_data.product_title|truncate:60}</span>

        <div class="media_text">
                    <span id="truncated_product_{$item.action_item_id}">
               <p>
                   {$item.action_data.product_body|jrCore_strip_html|truncate:400}
                   {if strlen($item.action_data.product_body) > 400}
                       <span class="more"><a href="#" onclick="showMore('product_{$item.action_item_id}')">More</a></span>
                   {/if}
               </p>
            </span>

            <span id="full_product_{$item.action_item_id}" style="display: none;"><p>
                    {$item.action_data.product_body|jrCore_strip_html}
                    <span class="more"><a href="#"
                                          onclick="showMore('product_{$item.action_item_id}')">Less</a></span>
                </p></span>
        </div>
        <br>
        <span class="location">{$item.action_data.product_category|jrCore_strip_html|truncate:60}</span>
    </div>
</div>