{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items)}
    {foreach from=$_items item="item"}

        {$row = 6}
        {$class = "col2"}
        {if jrCore_is_tablet_device()}
            {$row = 4}
            {$class = "col3"}
        {/if}

        {if $item.list_rank%$row == 1}
            <div class="row">
        {/if}
        <div class="{$class}">
            <div class="p10">
                <div class="image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_image_name}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" class="img_scale" crop="16:9" alt=$item.gallery_title}</a>
                    <div class="hover">
                        <div class="table">
                            <div class="table-row">
                                <div class="table-cell">
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_title_url}" title="{jrCore_lang skin="jrElastic2" id=72 default="View Gallery"}">{jrCore_icon icon="gallery" size="40" color="ffffff"}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="center">
                <span><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.gallery_title_url}">{$item.gallery_title|truncate:28}</a></span>
            </div>
        </div>

        {if $item.list_rank%$row == 0 || $item.list_rank == $info.total_items}
            </div>
        {/if}
    {/foreach}
{/if}
