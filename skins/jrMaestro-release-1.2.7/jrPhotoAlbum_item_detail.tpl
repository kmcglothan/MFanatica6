{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
{jrCore_module_url module="jrGallery"    assign="gurl"}
{jrCore_module_url module="jrFlickr"     assign="furl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMaestro_breadcrumbs module="jrPhotoAlbum" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrPhotoAlbum" item=$item}
    </div>
</div>

<div class="box">
    {jrMaestro_sort template="icons.tpl" nav_mode="jrPhotoAlbum" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap detail_section">
            <div class="media">
                <div class="wrap">
                    {if isset($item)}
                        <div class="block_content">
                            <div>
                                {if !jrCore_is_mobile_device()}
                                    <div class="image_nav" style="float: right;">
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
                                        <a href="{$jamroom_url}/{$gurl}/image/gallery_image/{$item['photoalbum_items']["`$fk`"]['_item_id']}/1280" data-lightbox="images" title="{$item['photoalbum_items']["`$fk`"]['gallery_alt_text']}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3 style="display: inline-block;margin: 0 0 1em;">{jrCore_lang module="jrPhotoAlbum" id="18" default="View images in Lightbox"}</h3></a>
                                    {elseif $item['photoalbum_items']["`$fk`"]['module'] == 'jrFlickr'}
                                        {assign var="_data" value=$item['photoalbum_items']["`$fk`"]['flickr_data']|json_decode:TRUE}
                                        <a href="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" data-lightbox="images" title="{$item['photoalbum_items']["`$fk`"]['flickr_title']}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3 style="display: inline-block;margin: 0 0 1em;">{jrCore_lang module="jrPhotoAlbum" id="18" default="View images in Lightbox"}</h3></a>
                                    {/if}
                                </div>
                                <div style="clear:both"></div>
                            </div>
                            <ul class="sortable grid">
                                {foreach $item.photoalbum_items as $key => $itm}
                                    <li data-id="{$itm.module}-{$itm._item_id}" style="width:33%">
                                        <div id="pa{$key}">
                                            <div id="p{$key}" class="p5" style="position:relative">
                                                {if $key > $fk}
                                                    {if $itm['module'] == 'jrGallery'}
                                                        <a href="{$jamroom_url}/{$gurl}/image/gallery_image/{$itm._item_id}/1280" data-lightbox="images" title="{$itm.gallery_alt_text}"></a>
                                                    {elseif $itm['module'] == 'jrFlickr'}
                                                        {assign var="_data" value=$itm.flickr_data|json_decode:TRUE}
                                                        <a href="{jrCore_server_protocol}://farm{$_data.attributes.farm}.staticflickr.com/{$_data.attributes.server}/{$_data.attributes.id}_{$_data.attributes.secret}.jpg" data-lightbox="images" title="{$itm.flickr_title}"></a>
                                                    {/if}
                                                {/if}
                                                {if $itm.module == 'jrGallery'}
                                                    <a href="{jrGallery_get_gallery_image_url item=$itm}" title="{$itm.gallery_alt_text}">
                                                        {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$itm._item_id size="xlarge" crop="auto" class="gallery_img iloutline" alt=$itm.gallery_alt_text width=false height=false}</a><br>
                                                {if jrProfile_is_profile_owner($item._profile_id)}
                                                    <script>$(function () {
                                                            $('#p{$key}').hover(function () {
                                                                $('#m{$key}').fadeToggle('fast');
                                                            });
                                                        });</script>
                                                    <div id="m{$key}" class="photoalbum_actions">
                                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrGallery"}
                                                        {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$item._item_id photoalbum_for="jrGallery" dom_id="pa{$key}" photo_id=$itm._item_id}
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
                                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$itm._item_id photoalbum_for="jrFlickr"}
                                                        {jrCore_module_function function="jrPhotoAlbum_delete_button" item_id=$item._item_id photoalbum_for="jrFlickr" dom_id="pa{$key}" photo_id=$itm._item_id}
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
                    {/if}
                </div>
            </div>

            {* bring in module features *}
            <div class="action_feedback">
                {jrMaestro_feedback_buttons module="jrPhotoAlbum" item=$item}
                {if jrCore_module_is_active('jrRating')}
                    <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                        function="jrRating_form"
                        type="star"
                        module="jrAudio"
                        index="1"
                        item_id=$item._item_id
                        current=$item.audio_rating_1_average_count|default:0
                        votes=$item.audio_rating_1_number|default:0}</div>
                {/if}
                {jrCore_item_detail_features module="jrPhotoAlbum" item=$item}
            </div>
        </div>
    </div>
</div>

<style type="text/css">
    .sortable {
        margin: 0;
        padding: 0;
        -webkit-touch-callout: none;
        -webkit-user-select: none;
        -khtml-user-select: none;
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
        float: left;
    }
</style>

{* We want to allow the item owner to re-order *}
{if jrProfile_is_profile_owner($_post._profile_id)}
    <style type="text/css">
        .sortable li { cursor: move; }
        li.sortable-placeholder { border: 2px dashed #BBB; background: none; height: 62px; width: 16%; margin: 13px; }
    </style>
    <script type="text/javascript">
        $(function () {
            $('.sortable').sortable().bind('sortupdate', function (event, ui) {
                // Triggered when the user stopped sorting and the DOM position has changed.
                var o = $('ul.sortable li').map(function () {
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrPhotoAlbum_url + "/order_update/id={$_post._1}/__ajax=1", {
                    photoalbum_order: o
                });
            });
        });
    </script>
{/if}

