{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items)}
    <div class="container">
        {foreach from=$_items item="item"}
            {if $item@first || ($item@iteration % 4) == 1}
                <div class="row">
            {/if}
            <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                <div class="p5">
                    <div class="img-profile">
                        {if $item@iteration > 1}
                            <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/1280" data-lightbox="images" title="{$item.gallery_caption|default:$item.gallery_image_name|jrCore_entity_string}"></a>
                        {/if}
                        <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a><br>
                    </div>
                    <div class="center mb10">
                        <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}" class="bold">{if isset($item.gallery_image_title) && strlen($item.gallery_image_title) > 0}{$item.gallery_image_title|truncate:25:"...":false}{else}{$item.gallery_image_name|truncate:25:"...":true}{/if}</a><br>
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all" class="media_title">{$item.gallery_title}</a>
                    </div>
                </div>
            </div>
            {if $item@last || ($item@iteration % 4) == 0}
                </div>
            {/if}
        {/foreach}
    </div>
{/if}
