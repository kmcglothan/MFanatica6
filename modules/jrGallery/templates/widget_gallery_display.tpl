{$per_row = 12 / $gallery_cols}
{if isset($_items)}
    {jrCore_module_url module="jrGallery" assign="murl"}
    {foreach $_items as $item}
        {if $item@first || ($item@iteration % $per_row) == 1}
            <div class="row">
        {/if}
        <div class="col{$gallery_cols}">
            <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="auto" class="img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a>
        </div>
        {if ($item@iteration % $per_row) == 0 || $item@last}
            </div>
        {/if}

    {/foreach}

{else}
    <div class="col12 last center p10">
        {jrCore_lang module="jrGallery" id=45 default="no gallery images were found"}
    </div>
{/if}
