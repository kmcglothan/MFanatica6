{* default index for profile *}
{jrCore_module_url module="jrGallery" assign="murl"}
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
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>

        {if $item.action_mode == 'create'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data.gallery_title_url}/all">
                    {jrCore_lang module="jrGallery" id="23" default="Created a New Gallery"}.
                </a></span><br>
        {else}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data.gallery_title_url}/all">
                    {jrCore_lang module="jrGallery" id="39" default="Updated a Gallery"}.
                </a></span><br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="media">
    <div style="padding: 0.25em; position: relative">
    {* each image template *}
    {capture assign="imgs"}
    {literal}
        {if isset($_items)}

        {$_list_count = $info.total_items}

        {foreach $_items as $_i}

        {if $_list_count == 1}
            {$class = "single"}
            {$aspect = "16:9"}
            {$size = "xxxlarge"}
        {elseif $_list_count == 2}
            {$aspect = "8:9"}
            {$class = "double"}
            {$size = "xxlarge"}
        {elseif $_list_count == 3}
            {$aspect = "5.3:9"}
            {$class = "triple"}
            {$size = "xxlarge"}
        {else}
            {$class = "quads"}
            {$aspect = "4:3"}
            {$size = "xlarge"}
        {/if}

        {if $_i.list_rank > 4}
            {assign var="class" value="hidden"}
        {/if}
        <div class="list-item photo {$class}">
            <div>
                <div>
                    {jrCore_module_url module="jrGallery" assign="murl"}
                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_i._item_id}/1280" data-lightbox="images_{$_i.gallery_title_url}" title="{$_i.gallery_caption|default:$_i.gallery_image_name|jrGallery_title_name:$_i.gallery_caption}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module="jrGallery"
                        type="gallery_image"
                        item_id=$_i._item_id
                        size=$size
                        crop=$aspect
                        alt=$_i.gallery_alt_text
                        width=false
                        height=false}</a>
                    {if $_i.list_rank == 4}
                    <div class="list-info full">
                        {math equation="x-y" x=$_list_count y=3 assign="m"}
                        <span>+{$m}</span>
                    </div>
                    {/if}
                </div>
            </div>
        </div>
        {/foreach}
        <br clear="all" />
        {/if}
    {/literal}
    {/capture}
    {if isset($item.action_data)}
        {$item = $item.action_data}
    {else}
        {$item = $_items[0]}
    {/if}
    {if $item.quota_jrGallery_gallery_order != 'off'}
        {jrCore_list
        module="jrGallery"
        search1="gallery_title_url = `$item.gallery_title_url`"
        search2="_profile_id = `$item._profile_id`"
        template=$imgs
        order_by="gallery_order numerical_asc"
        limit=20
        }
    {else}
        {jrCore_list
        module="jrGallery"
        search1="gallery_title_url = `$item.gallery_title_url`"
        search2="_profile_id = `$item._profile_id`"
        template=$imgs
        order_by="_created numerical_desc"
        limit=20
        }
    {/if}
    </div>
</div>
