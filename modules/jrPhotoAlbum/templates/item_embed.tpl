{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_module_url module="jrGallery"    assign="gurl"}
{jrCore_module_url module="jrFlickr"     assign="furl"}

<div class="embedded_item">
    <div>
        <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}">{$item.photoalbum_title}</a></h2>
    </div>
    <div class="mt10" style="padding-top:0">
        {assign var="limit" value="6"}
        {if jrCore_is_mobile_device()}
            {assign var="limit" value="5"}
        {/if}
        {$i = 0}
        {foreach $item.photoalbum_items as $itm}
            {if $i >= $limit}
                {continue}
            {/if}
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}" target="_blank">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$itm._item_id size="small" crop="auto" class="iloutline"}</a>
            {$i = $i+1}
        {/foreach}
        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}" target="_blank"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
        <br>
        <span class="info">{jrCore_lang module="jrPhotoAlbum" id="14" default="Photos"}:</span> <span class="info_c">{$item.photoalbum_count}</span>
    </div>
    <br style="clear:left">
</div>
