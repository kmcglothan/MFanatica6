{if isset($bundle_item)}

    {jrCore_module_url module="jrVideo" assign="murl"}
    <div class="container">
        <div class="row">
            <div class="col2">
                <div class="block_image">
                    <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$bundle_item._item_id size="small" crop="auto" class="iloutline" alt=$bundle_item.video_title width=false height=false}</a>
                </div>
            </div>
            <div class="col6">
                <div class="p5">
                    <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.video_title_url}">{$bundle_item.video_title}</a></h3><br>
                    <span class="info">{jrCore_lang module="jrVideo" id="31" default="album"}:</span> <span class="info_c"><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/albums/{$bundle_item.video_album_url}">{$bundle_item.video_album}</a></span>
                </div>
            </div>
            <div class="col2">
                <div class="p5">
                    {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$bundle_item._item_id current=$bundle_item.video_rating_1_average_count|default:0 votes=$bundle_item.video_rating_1_number|default:0}
                </div>
            </div>
            <div class="col2 last">
                <div class="block_config">
                    {jrCore_module_function function="jrFoxyCartBundle_button" module="jrVideo" field="video_file" item=$item}
                    {jrCore_module_function function="jrFoxyCartBundle_remove_button" id="#jrVideo`$bundle_item._item_id`" module="jrVideo" bundle_id=$bundle_id item=$bundle_item}
                </div>
            </div>
        </div>
    </div>

{/if}