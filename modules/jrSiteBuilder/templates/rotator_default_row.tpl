{if $_params.module == "jrProfile"}
    {if isset($_items)}
        {foreach $_items as $item}
            <li>
                <a href="{$jamroom_url}/{$item.profile_url}">{jrCore_module_function function="jrImage_display" module="jrProfile" type="profile_image" item_id=$item._profile_id size="xxlarge" crop="`$_params.crop`" class="img_scale" alt=$item.profile_name title=$item.profile_name}</a>

                <p class="caption">
                    <a href="{$jamroom_url}/{$item.profile_url}"><span style="color:#FFF;">{$item.profile_name}</span></a>
                </p>
            </li>
        {/foreach}
    {/if}
{/if}

{if $_params.module == "jrGallery"}
    {jrCore_module_url module="jrGallery" assign="murl"}
    {if isset($_items)}
        {foreach $_items as $item}
            <li>
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_title_url}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="xxlarge" crop="`$_params.crop`" class="img_scale" alt=$item.gallery_title title=$item.gallery_title}</a>

                <p class="caption">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_title_url}"><span style="color:#FFF;">{$item.gallery_title}</span></a>
                </p>
            </li>
        {/foreach}
    {/if}
{/if}
