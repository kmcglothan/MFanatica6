{jrCore_module_url module="jrFile" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
    <div class="item">
        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">{jrCore_module_function function="jrImage_display" module="jrFile" type="file_image" item_id=$item._item_id size="xlarge" crop="auto" class="iloutline img_scale" alt=$item.file_title width=false height=false}</a>
                    </div>
                </div>
                <div class="col6">
                    <div class="p5">
                        <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.file_title_url}">{$item.file_title}</a></h3><br>

                        <span class="info_c">
                        {if empty($item.file_file_item_price) && empty($item.file_file_item_bundle)}
                            <a href="{$jamroom_url}/{$murl}/download/file_file/{$item._item_id}">{$item.file_file_name}</a>
                        {else}
                            {$item.file_file_name}
                        {/if}
                        </span>
                        <br>{jrCore_module_function function="jrRating_form" type="star" module="jrFile" index="1" item_id=$item._item_id current=$item.file_rating_1_average_count|default:0 votes=$item.file_rating_1_count|default:0}
                    </div>
                </div>
                <div class="col4 last">
                    <div class="block_config">

                        {jrCore_item_list_buttons module="jrFile" field="file_file" item=$item}

                    </div>
                    <div class="clear"></div>
                </div>
            </div>

        </div>
    </div>
    {/foreach}
{/if}
