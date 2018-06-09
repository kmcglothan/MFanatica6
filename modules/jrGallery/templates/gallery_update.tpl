{jrCore_module_url module="jrGallery" assign="murl"}
{if isset($_items) && is_array($_items)}

    {jrCore_lang module="jrGallery" id=59 default="search images" assign="search_text"}
    <input id="searchbox1" type="text" class="form_text" placeholder="{$search_text|jrCore_entity_string}">

    <div style="clear:both" class="jrgallery_scroll_box">

    {* WE ARE IN THE DEFAULT VIEW *}
    {jrCore_lang module="jrGallery" id=58 default="(no title)" assign="notitle"}
    {foreach $_items as $key => $item}
        {jrGallery_get_gallery_image_title item=$item assign="gtitle"}
        <div class="jrgallery_update_div rounded" id="gi_{$item._item_id}" data-title="{$gtitle|jrCore_str_to_lower|jrCore_entity_string}" data-filename="{$item.gallery_image_name|jrCore_str_to_lower|jrCore_entity_string}">

            <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/xxxlarge/_v={$item.gallery_image_time}" target="_blank" data-lightbox="images" title="{$gtitle|jrCore_entity_string}">
                {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="medium" class="jrgallery_update_image" alt=$item.gallery_alt_text width=140 crop="4:3" _v=$item.gallery_image_time}
            </a>
            <br>

            <div class="gallery_image_info">
                <span id="gallery_image_title_{$item._item_id}" class="gallery_update_title">{if strlen($item.gallery_image_title) > 0}{$item.gallery_image_title}{else}<span class="gallery_no_title">{$notitle}</span>{/if}</span> <span style="float: right;"><a onclick="jrGallery_edit_title('{$item._item_id}');">{jrCore_icon icon="gear" size=10}</a></span><br>
                <span class="gallery_filename">{$item.gallery_image_name}</span>
            </div>

            <input type="button" class="jrgallery_update_button" value="{jrCore_lang module="jrGallery" id=20 default="detail"}" onclick="window.location='{$jamroom_url}/{$murl}/detail/id={$item._item_id}'">
            {jrCore_image image="form_spinner.gif" id="del_spin_{$item._item_id}" width="24" height="24" alt="working" style="margin:8px 8px 0px 8px;display:none"}
            <input type="button" class="jrgallery_update_button" id="del_btn_{$item._item_id}" value="{jrCore_lang module="jrGallery" id=21 default="delete"}" onclick="jrGallery_update_delete('{$item._item_id}')">

        </div>
    {/foreach}

    </div>

    <div id="gallery_title_modal" class="search_box" style="display:none">
        <div id="template-error" style="display:none"></div>
        <input id="gallery_update_id" type="hidden" value="">
        {jrCore_lang module="jrGallery" id=61 default="Image Title" assign="ititle"}
        <input id="gallery_update_title" type="text" class="form_text" placeholder="{$ititle|jrCore_entity_string}" onkeypress="if (event && event.keyCode == 13) { jrGallery_save_title(); }">&nbsp;
        <input id="gallery_title_save" type="button" value="save" class="form_button" onclick="jrGallery_save_title()">
        <div class="clear"></div>
        <div class="simplemodal-close" style="position:absolute;right:5px;bottom:5px">{jrCore_icon icon="close" size="16"}</div>
    </div>

    <script type="text/javascript">

        function jrGallery_edit_title(id)
        {
            $('#gallery_update_id').val(id);
            var t = $('#gallery_image_title_' + id).text();
            if (t === "{$notitle}") {
                t = '';
            }
            $('#gallery_update_title').val(t);
            $('#gallery_title_modal').modal()
        }
        jQuery(function($)
        {
            $("#searchbox1").livesearch({
                innerText: "{$search_text|jrCore_entity_string}",
                searchCallback: jrGallerySearch
            });
            function jrGallerySearch(term)
            {
                term = term.toLowerCase();
                var i = $('.jrgallery_update_div');
                i.removeClass('gallery_search_found').show();
                if (term != "") {
                    if (i.filter('div[data-title*="' + term + '"]').length > 0 || i.filter('div[data-filename*="' + term + '"]').length > 0) {
                        $('#searchbox1').removeClass('error');
                        i.filter('div[data-title*="' + term + '"]').addClass('gallery_search_found');
                        i.filter('div[data-filename*="' + term + '"]').addClass('gallery_search_found');
                        i.not('.gallery_search_found').fadeOut();
                    }
                    else {
                        $('#searchbox1').addClass('error');
                        i.removeClass('gallery_search_found').show();
                    }
                }
                else {
                    $('#searchbox1').removeClass('error');
                    i.removeClass('gallery_search_found').show();
                }
            }
        });

    </script>
{/if}
