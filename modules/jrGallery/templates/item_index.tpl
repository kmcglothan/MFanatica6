{jrCore_module_url module="jrGallery" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrGallery" profile_id=$_profile_id}
        </div>
        {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
            {jrCore_lang module="jrGallery" id=38 default="Images" assign="heading"}
        {else}
            {jrCore_lang module="jrGallery" id=24 default="Image Galleries" assign="heading"}
        {/if}
        <h1>{if isset($_post._1) && strlen($_post._1) > 0}{$_post._1}{else}{$heading}{/if}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{if isset($_post._1) && strlen($_post._1) > 0}{$_post._1}{else}{$heading}{/if}</a>
        </div>
    </div>

    <div class="block_content">

        {if isset($quota_jrGallery_gallery_group) && $quota_jrGallery_gallery_group == 'off'}
            {capture name="template" assign="tpl"}
            {literal}
            {jrCore_module_url module="jrGallery" assign="murl"}

            <div class="block">

                <div class="gallery_lightbox">
                    {jrGallery_get_gallery_image_title item=$_items[0] assign="gtitle"}
                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$_items[0]._item_id}/1280" data-lightbox="images" title="{$item.gallery_caption|default:$gtitle}">{jrCore_icon icon="search2"}&nbsp;&nbsp;<h3>{jrCore_lang module="jrGallery" id="37" default="View images in Lightbox"}</h3></a>
                </div>

                <div style="clear:both"></div>

            </div>

            {if isset($_items)}
            <div class="container">
                {foreach from=$_items item="item"}
                {if $item@first || ($item@iteration % 4) == 1}
                <div class="row">
                {/if}
                    <div class="col3{if $item@last || ($item@iteration % 4) == 0} last{/if}">
                        <div class="p5">
                            <div id="pai-{$item._item_id}" class="img-profile">
                                {if $item@iteration > 1}
                                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/1280" data-lightbox="images" title="{$item.gallery_caption|default:$item.gallery_image_name|jrCore_entity_string}"></a>
                                {/if}
                                <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a>

                                {if (jrCore_module_is_active('jrPhotoAlbum')) }
                                <script>
                                    $(function() {
                                        var mid = $('#pa-add-{$item._item_id}');
                                        $('#pai-{$item._item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } );
                                    } );
                                </script>
                                <div id="pa-add-{$item._item_id}" class="gallery_actions">
                                    {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery" size=20}
                                </div>
                                {/if}

                            </div>
                            <div class="center mb10">
                                {jrGallery_get_gallery_image_title item=$item assign="gtitle"}
                                <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}" class="bold">{$gtitle|truncate:25:"...":true}</a><br>
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all" class="media_title">{$item.gallery_title}</a>
                            </div>
                        </div>
                    </div>
                    {if $item@last || ($item@iteration % 4) == 0}
                </div>
                {/if}
                {/foreach}
            </div>
            {/if}

            {/literal}
            {/capture}

            {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_updated desc" pagebreak=16 page=$_post.p pager=true template=$tpl}

            {jrShareThis module="jrGallery"}

        {else}

            <div class="block">

                <div class="gallery_view_all">
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}/all/p=1"><h3>{jrCore_lang module="jrGallery" id="48" default="View All Images"}</h3>&nbsp;&nbsp;{jrCore_icon icon="arrow-right"}</a>
                </div>

                <div style="clear:both"></div>

            </div>

            {jrCore_list module="jrGallery" profile_id=$_profile_id order_by="_updated desc" group_by="gallery_title_url" pagebreak=6 page=$_post.p pager=true}

        {/if}

    </div>


</div>
