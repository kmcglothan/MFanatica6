
{if isset($bundle_item)}
    {jrCore_module_url module="jrGallery" assign="murl"}
    <div class="col4">
        <div class="item">
            {if jrProfile_is_profile_owner($item._profile_id)}
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell" style="text-align: right">
                            {if jrCore_module_is_active('jrFoxyCartBundle')}
                                {jrCore_module_function function="jrFoxyCartBundle_button" module="jrAudio" field="audio_file" item=$bundle_item}
                                {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrAudio`$bundle_item._item_id`" module="jrAudio" bundle_id=$bundle_id item=$bundle_item}
                            {elseif jrCore_module_is_active('jrBundle')}
                                {jrCore_lang module="jrBundle" id=31 default="remove from bundle" assign="dlt"}
                                {jrCore_lang module="jrBundle" id=32 default="Are you sure you want to remove this item from this bundle?" assign="dlp"}
                                <a title="{$dlt}" onclick="jrCore_confirm('{$dlt|addslashes}', '{$dlp|addslashes}', function() { jrBundle_remove({$item._item_id}, '{$bundle_item.bundle_module}', '{$bundle_item._item_id}'); } )">{jrCore_icon icon="close" size=20}</a>
                            {/if}
                        </div>
                    </div>
                </div>
            {/if}
            <div class="bundle-image">
                {if $bundle_item.bundle_only == 'on'}
                    {* this item is only available in this bundle *}
                    <div class="bundle_only">
                        <i>{jrCore_lang module="jrBundle" id=39 default="Available only as part of this bundle!"}</i>
                    </div>
                {/if}
                <a href="{jrGallery_get_gallery_image_url item=$bundle_item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$bundle_item._item_id size="large" crop="auto" class="img_scale iloutline" alt=$bundle_item.gallery_alt_text width=false height=false}</a>
            </div>
            <div class="bundle-item-info">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell">
                            <h3><a href="{jrGallery_get_gallery_image_url item=$bundle_item}">
                                    {if isset($bundle_item.gallery_image_title)}
                                        {$bundle_item.gallery_image_title}
                                    {else}
                                        {$bundle_item.gallery_image_name}
                                    {/if}
                                </a></h3>
                            <span class="info">Gallery</span> {$bundle_item.gallery_title}
                        </div>
                        <div class="table-cell">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$bundle_item._item_id current=$bundle_item.gallery_rating_1_average_count|default:0 votes=$bundle_item.gallery_rating_1_number|default:0}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
