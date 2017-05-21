{if isset($bundle_item)}

    {jrCore_module_url module="jrFile" assign="murl"}
    <div class="container">
        <div class="row">
            <div class="col2">
                <div class="block_image">
                    <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.file_title_url}">{jrCore_module_function function="jrImage_display" module="jrFile" type="file_image" item_id=$bundle_item._item_id size="small" crop="auto" class="iloutline" alt=$bundle_item.file_title width=false height=false}</a>
                </div>
            </div>
            <div class="col6">
                <div class="p5">
                    <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.file_title_url}">{$bundle_item.file_title}</a></h3><br>
                    <span class="info">{jrCore_lang module="jrFile" id="14" default="size"}:</span> <span class="info_c">{$bundle_item.file_file_size|jrCore_format_size}</span>
                </div>
            </div>
            <div class="col2">
                <div class="p5">
                    {jrCore_module_function function="jrRating_form" type="star" module="jrFile" index="1" item_id=$bundle_item._item_id current=$bundle_item.file_rating_1_average_count|default:0 votes=$bundle_item.file_rating_1_number|default:0}
                </div>
            </div>
            <div class="col2 last">
                <div class="block_config">
                    {jrCore_module_function function="jrFoxyCartBundle_button" module="jrFile" field="file_file" item=$item}
                    {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrFile`$bundle_item._item_id`" module="jrFile" bundle_id=$bundle_id item=$bundle_item}
                </div>
            </div>
        </div>
    </div>

{/if}