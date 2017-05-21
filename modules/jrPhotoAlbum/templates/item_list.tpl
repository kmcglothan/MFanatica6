{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_module_url module="jrGallery" assign="gurl"}
{if isset($_items)}
    {foreach $_items as $item}
        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col10">
                        <div class="jr_photoalbum_row">
                            <div>
                                <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}">{$item.photoalbum_title}</a></h2>
                            </div>
                            <div class="mt10" style="padding-top:0">
                                {assign var="limit" value="6"}
                                {if jrCore_is_mobile_device()}
                                    {assign var="limit" value="5"}
                                {/if}
                                {$i = 0}
                                {foreach $item.photoalbum_photos as $img_id}
                                    {if $i >= $limit}
                                        {continue}
                                    {/if}
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$img_id size="small" crop="auto" class="iloutline"}</a>
                                    {$i = $i+1}
                                {/foreach}
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.photoalbum_title_url}"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
                                <br>
                                <span class="info">{jrCore_lang module="jrPhotoAlbum" id="14" default="Photos"}:</span> <span class="info_c">{$item.photoalbum_count}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col2 last">
                        <div class="block_config">
                            {jrCore_item_list_buttons module="jrPhotoAlbum" item=$item}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    {/foreach}
{/if}