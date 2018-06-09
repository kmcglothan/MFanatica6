{if isset($bundle_item)}
    {jrCore_module_url module="jrProduct" assign="murl"}
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
                <a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.video_title_url}">{jrCore_module_function function="jrImage_display" module="jrProduct" type="video_image" item_id=$bundle_item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$bundle_item.video_title width=false height=false}</a>
            </div>
            <div class="bundle-item-info">
                <div class="table">
                    <div class="table-row">
                        <div class="table-cell">
                            <h3><a href="{$jamroom_url}/{$bundle_item.profile_url}/{$murl}/{$bundle_item._item_id}/{$bundle_item.product_title_url}">{$bundle_item.product_title}</a></h3>
                        </div>
                        <div class="table-cell">
                            {jrCore_module_function function="jrRating_form" type="star" module="jrVideo" index="1" item_id=$bundle_item._item_id current=$bundle_item.video_rating_1_average_count|default:0 votes=$bundle_item.video_rating_1_number|default:0}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/if}
