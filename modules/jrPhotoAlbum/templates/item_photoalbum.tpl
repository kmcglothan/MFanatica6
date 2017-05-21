{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_module_url module="jrGallery"    assign="gurl"}
{jrCore_module_url module="jrFlickr"     assign="furl"}

{if isset($_items)}
    <div class="block_content">
        <div>
            {if !jrCore_is_mobile_device()}
                <div style="float:right;margin-top:12px;">
                    <a onclick="jrPhotoAlbum_xup(2)" class="form_button">2</a>
                    <a onclick="jrPhotoAlbum_xup(3)" class="form_button">3</a>
                    <a onclick="jrPhotoAlbum_xup(4)" class="form_button">4</a>
                    <a onclick="jrPhotoAlbum_xup(6)" class="form_button">6</a>
                    <a onclick="jrPhotoAlbum_xup(8)" class="form_button">8</a>
                </div>
            {/if}
            <div style="clear:both"></div>
        </div>
        <ul class="sortable grid">
            {foreach $_items as $key => $itm}
                <li data-id="{$key}" style="width:33%">
                    <div id="pa{$key}">
                        <div id="p{$key}" class="p5" style="position:relative">
                            {if $itm.module == 'jrGallery'}
                                <a href="{jrGallery_get_gallery_image_url item=$itm}" title="{$itm.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$itm._item_id size="medium" crop="auto" class="gallery_img iloutline" alt=$itm.gallery_alt_text width=false height=false}</a><br>
                                <script>$(function () {
                                        $('#p{$key}').hover(function () {
                                            $('#m{$key}').fadeToggle('fast');
                                        });
                                    });</script>
                                <div id="m{$key}" class="gallery_actions">
                                    {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrGallery" size=20}
                                    {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$_post._1 photoalbum_for="jrGallery" size=20 dom_id="pa{$key}" photo_id=$itm._item_id}
                                </div>
                            {elseif $itm.module == 'jrFlickr'}
                                {$_fdata = json_decode($itm['flickr_data'], true)}
                                <a href="{$jamroom_url}/{$itm.profile_url}/{$furl}/{$itm._item_id}/{$itm.flickr_title_url}" title="{$itm.flickr_alt_text}"><img src="{jrCore_server_protocol}://farm{$_fdata.attributes.farm}.staticflickr.com/{$_fdata.attributes.server}/{$_fdata.attributes.id}_{$_fdata.attributes.secret}.jpg" width="100%" alt="{$pa_item.flickr_title}"></a><br>
                                <script>$(function () {
                                        $('#p{$key}').hover(function () {
                                            $('#m{$key}').fadeToggle('fast');
                                        });
                                    });</script>
                                <div id="m{$key}" class="gallery_actions">
                                    {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrFlickr" size=20}
                                    {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$_post._1 photoalbum_for="jrFlickr" size=20 dom_id="pa{$key}" photo_id=$itm._item_id}
                                </div>
                            {/if}
                        </div>
                    </div>
                </li>
            {/foreach}
        </ul>
        {jrCore_include module="jrCore" template="list_pager.tpl"}
    </div>
    {jrShareThis module="jrPhotoAlbum"}
{/if}

<style type="text/css">
    .sortable {
        margin: 0;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    .sortable.grid {
        overflow: hidden;
    }
    .sortable li {
        margin: 0 auto;
        list-style: none;
        display: inline-block;
        width: 32%;
    }
</style>
