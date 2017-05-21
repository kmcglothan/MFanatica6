{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_module_url module="jrGallery"    assign="gurl"}
{jrCore_module_url module="jrFlickr"     assign="furl"}
<div class="block">
    {if isset($item)}
        <div class="title">
            <div class="block_config">
                {jrCore_item_detail_buttons module="jrPhotoAlbum" item=$item}
            </div>
            <h1>{$item.photoalbum_title}</h1>
            <div class="breadcrumbs">
                <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}</a> &raquo; {$item.photoalbum_title}
            </div>
        </div>
        <div class="block_content">
            <div class="block">
                {if !jrCore_is_mobile_device()}
                    <div style="float:right;margin-top:12px;">
                        <a onclick="jrPhotoAlbum_xup(2)" class="form_button">2</a>
                        <a onclick="jrPhotoAlbum_xup(3)" class="form_button">3</a>
                        <a onclick="jrPhotoAlbum_xup(4)" class="form_button">4</a>
                        <a onclick="jrPhotoAlbum_xup(6)" class="form_button">6</a>
                        <a onclick="jrPhotoAlbum_xup(8)" class="form_button">8</a>
                    </div>
                {/if}
                <div class="gallery_lightbox">
                    {$ignore = $item['photoalbum_items']|reset}
                    {$fk = key($item['photoalbum_items'])}
                    {if $item['photoalbum_items']["`$fk`"]['module'] == 'jrGallery'}
                        <a href="{$jamroom_url}/{$gurl}/image/gallery_image/{$item['photoalbum_items']["`$fk`"]['_item_id']}/1280" data-lightbox="images" title="{$item['photoalbum_items']["`$fk`"]['gallery_alt_text']}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3>{jrCore_lang module="jrPhotoAlbum" id="18" default="View images in Lightbox"}</h3></a>
                    {elseif $item['photoalbum_items']["`$fk`"]['module'] == 'jrFlickr'}
                        {assign var="_data" value=$item['photoalbum_items']["`$fk`"]['flickr_data']|json_decode:TRUE}
                        <a href="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" data-lightbox="images" title="{$item['photoalbum_items']["`$fk`"]['flickr_title']}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3>{jrCore_lang module="jrPhotoAlbum" id="18" default="View images in Lightbox"}</h3></a>
                    {/if}
                </div>
                <div style="clear:both"></div>
            </div>
            <ul class="sortable grid">
                {foreach $item.photoalbum_items as $key => $itm}
                    <li data-id="{$itm.module}-{$itm._item_id}" style="width:{$smarty.cookies.jr_photoalbum_xup_width|default:"24.5"}%">
                        <div id="pa{$key}">
                            <div id="p{$key}" class="p5" style="position:relative">

                                {if $key > $fk}
                                    {if $itm.module == 'jrGallery'}
                                        <a href="{$jamroom_url}/{$gurl}/image/gallery_image/{$itm._item_id}/1280" data-lightbox="images" title="{$itm.gallery_alt_text}"></a>
                                    {elseif $itm.module == 'jrFlickr'}
                                        {assign var="_data" value=$itm.flickr_data|json_decode:TRUE}
                                        <a href="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" data-lightbox="images" title="{$itm.flickr_title}"></a>
                                    {/if}
                                {/if}

                                {if $itm.module == 'jrGallery'}
                                    <a href="{jrGallery_get_gallery_image_url item=$itm}" title="{$itm.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$itm._item_id size="larger" crop="auto" class="gallery_img iloutline" alt=$itm.gallery_alt_text width=false height=false}</a><br>
                                    {if jrProfile_is_profile_owner($item._profile_id)}
                                    <script>$(function () {
                                            $('#p{$key}').hover(function () {
                                                $('#m{$key}').fadeToggle('fast');
                                            });
                                        });</script>
                                    <div id="m{$key}" class="photoalbum_actions">
                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrGallery" size=20} {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$item._item_id photoalbum_for="jrGallery" dom_id="pa{$key}" photo_id=$itm._item_id size=20}
                                    </div>
                                    {/if}

                                {elseif $itm.module == 'jrFlickr'}
                                    {$_fdata = json_decode($itm['flickr_data'], true)}
                                    <a href="{$jamroom_url}/{$itm.profile_url}/{$furl}/{$itm._item_id}/{$itm.flickr_title_url}" title="{$itm.flickr_alt_text}"><img src="{jrCore_server_protocol}://farm{$_fdata.attributes.farm}.staticflickr.com/{$_fdata.attributes.server}/{$_fdata.attributes.id}_{$_fdata.attributes.secret}.jpg" width="100%" alt="{$pa_item.flickr_title}"></a><br>
                                    {if jrProfile_is_profile_owner($item._profile_id)}
                                    <script>$(function () {
                                            $('#p{$key}').hover(function () {
                                                $('#m{$key}').fadeToggle('fast');
                                            });
                                        });</script>
                                    <div id="m{$key}" class="photoalbum_actions">
                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrFlickr" size=20} {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$item._item_id photoalbum_for="jrFlickr" dom_id="pa{$key}" photo_id=$itm._item_id size=20}
                                    </div>
                                    {/if}

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
</div>

<style type="text/css">
    ul.sortable {
        margin: 0;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
    ul.sortable.grid {
        overflow: hidden;
    }
    ul.sortable > li {
        margin: 0 auto;
        list-style: none;
        display: inline-block;
    }
</style>

{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($item._profile_id)}
    <style type="text/css">
        ul.sortable li {
            cursor: move;
            padding: 0;
            margin: 0;
        }
        ul.sortable a {
            cursor: move;
        }
        .ui-sortable-placeholder {
            z-index: 100;
            padding: 0;
            margin: 0;
        }
        .ui-sortable-helper {
            padding: 0;
            margin: 0;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            $('ul.sortable').sortable().bind('sortstart', function (e, ui)
            {
                var h = ui.item.height();
                $('.ui-sortable-placeholder').css('width', h + 'px').css('height', 0);
                $(ui.item).css('width', h + 'px').css('height', h + 'px');
            }).bind('sortupdate', function (e, ui) {
                var o = $('ul.sortable > li').map(function () {
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrPhotoAlbum_url + "/order_update/id={$_post._1}/__ajax=1", {
                    photoalbum_order: o
                });
            });
        });
    </script>
{/if}
