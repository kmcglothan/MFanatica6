{jrCore_module_url module="jrGallery" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrISkin_breadcrumbs module="jrGallery" profile_url=$_items[0].profile_url profile_name=$_items[0].profile_name page="group" item=$_items[0]}
    </div>
    <div class="action_buttons">
        {if isset($_items[0].gallery_image_item_bundle)}
            {jrCore_db_get_item module="jrFoxyCartBundle" item_id=$_items[0].gallery_image_item_bundle assign="bundle"}
        {/if}
        {if is_array($bundle)}
            {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrFoxyCartBundle" field="bundle" quantity_max="1" price=$bundle.bundle_item_price no_bundle="true" item=$bundle}
        {/if}

        {if !$show_all_galleries}
            {jrCore_item_create_button module="jrGallery" profile_id=$_profile_id}
            {jrCore_item_update_button module="jrGallery" profile_id=$_profile_id item_id=$_items[0]._item_id}
            {jrCore_item_delete_button module="jrGallery" profile_id=$_profile_id action="`$murl`/delete_save/`$_items[0].profile_url`/`$_items[0].gallery_title_url`"}
        {/if}
    </div>
</div>

<div class="box">
    {jrISkin_sort template="icons.tpl" nav_mode="jrGallery" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap detail_section">
            <div class="media">
                <div class="wrap">
                    {if isset($_items)}
                        <div class="block_content">
                            <div>
                                {if !jrCore_is_mobile_device()}
                                    <div style="float:right;" class="image_nav">
                                        <a onclick="jrGallery_xup(2)" class="form_button">2</a>
                                        <a onclick="jrGallery_xup(3)" class="form_button">3</a>
                                        <a onclick="jrGallery_xup(4)" class="form_button">4</a>
                                        <a onclick="jrGallery_xup(6)" class="form_button">6</a>
                                        <a onclick="jrGallery_xup(8)" class="form_button">8</a>
                                    </div>
                                {/if}
                                <div style="float:left;">
                                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_items[0]._item_id}/1280" data-lightbox="images" title="{$_items['0'].gallery_alt_text}">{jrCore_icon icon="search2"}&nbsp;&nbsp;
                                        <h3 style="display: inline-block;margin: 0 0 1em;">{jrCore_lang module="jrGallery" id="37" default="View images in Lightbox"}</h3></a>
                                </div>
                                <div style="clear:both"></div>
                            </div>

                            <ul class="sortable grid">
                                {foreach $_items as $key => $item}
                                    <li data-id="{$item._item_id}" style="width:{$img_width}%">
                                        <div id="p{$item._item_id}" class="p5" style="position:relative">

                                            {if $item@iteration > 1}
                                                <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/1280" data-lightbox="images" title="{$item.gallery_alt_text}"></a>
                                            {/if}
                                            <a href="{jrGallery_get_gallery_image_url item=$item}"  title="{$item.gallery_alt_text}">
                                                {jrCore_module_function
                                                function="jrImage_display"
                                                module="jrGallery"
                                                type="gallery_image"
                                                item_id=$item._item_id
                                                size="xlarge"
                                                crop="auto"
                                                class="gallery_img iloutline"
                                                alt=$item.gallery_alt_text
                                                width=false
                                                height=false
                                                }</a>

                                            {if jrProfile_is_profile_owner($_profile_id)}
                                                <script>$(function() {
                                                        var mid = $('#m{$item._item_id}');
                                                        $('#p{$item._item_id}').hover(function() {
                                                                    mid.show();
                                                                }, function() {
                                                                    mid.hide();
                                                                }
                                                        );
                                                    });</script>
                                                <div id="m{$item._item_id}" class="gallery_actions">
                                                    {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery" size=20}
                                                    {jrCore_item_update_button module="jrGallery" action="`$murl`/detail/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id size=20}
                                                    {jrCore_item_delete_button module="jrGallery" action="`$murl`/delete_image/id=`$item._item_id`" profile_id=$item._profile_id item_id=$item._item_id size=20}
                                                </div>
                                            {elseif (jrCore_module_is_active('jrPhotoAlbum')) }
                                                {* users who dont own the profile but want to add images to a photo album*}
                                                <script>$(function() {
                                                        var mid = $('#m{$item._item_id}');
                                                        $('#p{$item._item_id}').hover(function() {
                                                                    mid.show();
                                                                }, function() {
                                                                    mid.hide();
                                                                }
                                                        );
                                                    });</script>
                                                <div id="m{$item._item_id}" class="gallery_actions">
                                                    {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery" size=20}
                                                </div>
                                            {/if}

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
                {jrISkin_feedback_buttons module="jrGallery" item=$item}
                {if jrCore_module_is_active('jrRating')}
                    <div class="rating" id="jrGallery_{$item._items.0}_rating">{jrCore_module_function
                        function="jrRating_form"
                        type="star"
                        module="jrGallery"
                        index="1"
                        item_id=$item._items.0
                        current=$item.gallery_rating_1_average_count|default:0
                        votes=$item.gallery_rating_1_number|default:0}</div>
                {/if}
                {jrCore_item_detail_features module="jrGallery" item=$_items[0]}
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
{if jrProfile_is_profile_owner($_profile_id) && $quota_jrGallery_gallery_order != 'off'}
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
                $.post(core_system_url + '/' + jrGallery_url + "/order_update/__ajax=1", {
                    gallery_order: o
                });
            });
        });
    </script>
{/if}
