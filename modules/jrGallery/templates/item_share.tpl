{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items)}
{foreach from=$_items item="item"}
    <div class="item">
        <div class="wrap">
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_title_url}">
                {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="xlarge" id="gallery_img_src" class="img_scale img_shadow" alt=$item.gallery_alt_text width=false height=false}
            </a>
        </div>
    </div>
{/foreach}
{/if}
