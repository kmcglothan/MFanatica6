
{if isset($bundle_item)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    <div class="col4">
        <div class="wrap">
            <div class="table">
                <div class="table-row">
                    <div class="table-cell" style="text-align: right">
                        {jrCore_module_function function="jrFoxyCartBundle_button" module="jrFile" field="file_file" item=$item}
                        {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrFile`$bundle_item._item_id`" module="jrFile" bundle_id=$bundle_id item=$bundle_item}
                    </div>
                </div>
            </div>
            <div class="bundle-image">
                <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.file_title_url}">{jrCore_module_function function="jrImage_display" module="jrFile" type="file_image" item_id=$bundle_item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$bundle_item.file_title width=false height=false}</a>
            </div>
            <div class="bundle-item-info">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell">
                            <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.file_title_url}">{$bundle_item.file_title}</a></h3>
                            <span class="info">{jrCore_lang module="jrFile" id="14" default="size"}:</span> <span class="info_c">{$bundle_item.file_file_size|jrCore_format_size}</span>
                        </div>
                        <div class="table-cell">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrFile" index="1" item_id=$bundle_item._item_id current=$bundle_item.file_rating_1_average_count|default:0 votes=$bundle_item.file_rating_1_number|default:0}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
