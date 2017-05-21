<div class="container">
    {if isset($_items)}
        {foreach from=$_items item="item"}

            {if $item@first || ($item@iteration % 4) == 1}
                <div class="row">
            {/if}
            <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                 <a href="{jrGallery_get_gallery_image_url item=$item}" title="@{$item.profile_url}: {$item.gallery_alt_text}" target="_blank">
                     {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="larger" crop="square" class="img_scale" style="margin:0" alt=$item.gallery_image_name}
                 </a>
            </div>
            {if $item@last || ($item@iteration % 4) == 0}
                </div>
            {/if}

        {/foreach}
    {/if}
</div>
