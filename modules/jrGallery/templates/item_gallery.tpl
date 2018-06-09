{jrCore_module_url module="jrGallery" assign="murl"}
<div class="block">

    {if isset($_items)}
        <div class="title">
            <div class="block_config">

                {jrCore_bundle_detail_buttons module="jrGallery" profile_id=$_items[0]._profile_id bundle_name=$_items[0].gallery_title create_action="`$murl`/create" update_action="`$murl`/update/id=`$_items[0]._item_id`" delete_action="`$murl`/delete_save/`$_items[0].profile_url`/`$_items[0].gallery_title_url`"}

            </div>

            {if isset($_post._1) && $_post._1 == 'all'}
                <h1>{jrCore_lang module="jrGallery" id=38 default="Images"}</h1>
            {else}
                <h1>{$_items[0].gallery_title}</h1>
            {/if}

            <div class="breadcrumbs">
                {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
                    {jrCore_lang module="jrGallery" id=38 default="Images" assign="heading"}
                {else}
                    {jrCore_lang module="jrGallery" id=24 default="Image Galleries" assign="heading"}
                {/if}
                {if $show_all_galleries}
                <a href="{$jamroom_url}/{$_items[0].profile_url}/">{$_items[0].profile_name}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}">{$heading}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}/all">All</a>
                {else}
                <a href="{$jamroom_url}/{$_items[0].profile_url}/">{$_items[0].profile_name}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}">{$heading}</a> &raquo; <a href="{$jamroom_url}/{$_items[0].profile_url}/{$murl}/{$_items[0].gallery_title_url}/all">{$_items[0].gallery_title}</a>
                {/if}
            </div>
        </div>
        <div class="block_content">

            <div class="block">
                {if !jrCore_is_mobile_device()}
                <div style="float:right;margin-top:12px;">
                    <a onclick="jrGallery_xup(2)" class="form_button">2</a>
                    <a onclick="jrGallery_xup(3)" class="form_button">3</a>
                    <a onclick="jrGallery_xup(4)" class="form_button">4</a>
                    <a onclick="jrGallery_xup(6)" class="form_button">6</a>
                    <a onclick="jrGallery_xup(8)" class="form_button">8</a>
                </div>
                {/if}
                <div class="gallery_lightbox">
                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_items[0]._item_id}/1280" data-lightbox="images" title="{$_items['0'].gallery_alt_text}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3>{jrCore_lang module="jrGallery" id="37" default="View images in Lightbox"}</h3></a>
                </div>
                <div style="clear:both"></div>
            </div>

            <ul class="sortable grid">
                {foreach $_items as $key => $item}
                    {if $item.gallery_pending != 1}
                    <li data-id="{$item._item_id}" style="width:{$img_width}%">
                        <div id="p{$item._item_id}" class="p5" style="position:relative">

                            {if $item@iteration > 1}
                                {jrGallery_get_gallery_image_title item=$item assign="gtitle"}
                                <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/1280" data-lightbox="images" title="{$gtitle|jrCore_entity_string}"></a>
                            {/if}
                            <a href="{jrGallery_get_gallery_image_url item=$item}"  title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="xxlarge" crop="auto" class="gallery_img iloutline" alt=$item.gallery_alt_text width=false height=false}</a><br>

                            <div class="gallery_rating">
                                {jrCore_module_function function="jrRating_form" type="star" module="jrGallery" index="1" item_id=$item._item_id current=$item.gallery_rating_1_average_count|default:0 votes=$item.gallery_rating_1_count|default:0}
                            </div>

                            {if jrCore_module_is_active('jrPhotoAlbum') && isset($_user.quota_jrPhotoAlbum_allowed) && $_user.quota_jrPhotoAlbum_allowed == 'on'}

                                {* admins and profile owners *}
                                {if jrProfile_is_profile_owner($_profile_id)}

                                    <script>$(function() { var mid = $('#m{$item._item_id}'); $('#p{$item._item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); });</script>
                                    <div id="m{$item._item_id}" class="gallery_actions">
                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery" size=20} {jrCore_item_update_button module="jrGallery" action="`$murl`/detail/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id size=20} {jrCore_item_delete_button module="jrGallery" action="`$murl`/delete_image/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id size=20}
                                    </div>

                                {* users who dont own the profile but want to add images to a photo album*}
                                {elseif (isset($_user.quota_jrPhotoAlbum_allowed) && $_user.quota_jrPhotoAlbum_allowed == 'on') || (!jrUser_is_logged_in() && $_conf.jrPhotoAlbum_require_login == 'off')}

                                    <script>$(function() { var mid = $('#m{$item._item_id}'); $('#p{$item._item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); });</script>
                                    <div id="m{$item._item_id}" class="gallery_actions">
                                        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery" size=20}
                                    </div>

                                 {/if}

                            {/if}

                        </div>
                    </li>
                    {/if}
                {/foreach}
            </ul>

            {jrCore_include module="jrCore" template="list_pager.tpl"}

        </div>

        {jrShareThis module="jrGallery"}

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
{if jrProfile_is_profile_owner($_profile_id) && $quota_jrGallery_gallery_order != 'off'}
    <style type="text/css">
        ul.sortable li {
            cursor: move;
            padding: 0;
            margin: 0;
        }
        ul.sortable a {
            cursor: move;
        }
        li.sortable-placeholder {
            width: 80px;
            height: 80px;
            z-index: 100;
            border: 1px dashed #BBB;
            margin: 0;
        }
    </style>
    <script type="text/javascript">
        $(function() {
            $('.sortable').sortable().bind('sortupdate', function() {
                var o = $('ul.sortable > li').map(function () {
                    return $(this).data("id");
                }).get();
                $.post(core_system_url + '/' + jrGallery_url + "/order_update/__ajax=1", {
                    gallery_order: o
                });
            });
        });
    </script>

{/if}
