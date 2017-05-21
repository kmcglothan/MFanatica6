{jrCore_module_url module="jrGallery" assign="murl"}
<script type="text/javascript">
    $(document).ready(function() {
        $.each(list, function(k, v) {
            $('#gallery_id_' + v).prop('checked', 'checked');
        });
    });
</script>

<div id="jrGallery_holder">

    {if isset($_items) && is_array($_items)}

        <div class="container">
            {foreach from=$_items item="item"}
            {if $item@iteration === 1 || ($item@iteration % 4) === 1}
                <div class="row">
            {/if}
            <div class="col3{if ($item@iteration % 4) === 0} last{/if}">
                <div class="p5 center">
                    <a href="{$jamroom_url}/{$murl}/image/gallery_image/{$item._item_id}/xxxlarge/v={$item._updated}" data-lightbox="images">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="3:2" class="img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a><br>
                    <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}" target="_blank">
                    {if isset($item.gallery_image_title)}
                        {$item.gallery_image_title|truncate:25:"...":false}
                    {else}
                        {$item.gallery_image_name|truncate:25:"...":true}
                    {/if}
                    </a><br><a onclick="jrGallery_widget_load_images(1,'_profile_id:{$item._profile_id}');">@{$item.profile_url}</a><br>
                    <label><input type="checkbox" id="gallery_id_{$item._item_id}" class="form_checkbox" value="" title="Add to Image List" onclick="jrGallery_include('{$item._item_id}')"> include</label>
                </div>
            </div>
            {if ($item@iteration % 4) === 0 || $item@last}
                </div>
            {/if}
            {/foreach}
        </div>

        {* prev/next page footer links *}
        {if $info.prev_page > 0 || $info.next_page > 0}
            <div class="block">
                <table style="width:100%">
                    <tr>
                        <td style="width:25%">
                            {if $info.prev_page > 0}
                                <a onclick="jrGallery_widget_load_images({$info.prev_page},'{$_post.sstr}','{$_post.ids}','false');">{jrCore_icon icon="previous"}</a>
                            {/if}
                        </td>
                        <td style="width:50%;text-align:center">
                            <form name="form" method="post" action="_self">
                                <select name="pagenum" class="form_select list_pager" style="width:60px;" onchange="jrGallery_widget_load_images($(this).val(),'{$_post.sstr}','{$_post.ids}','false');">
                                    {for $pages=1 to $info.total_pages}
                                        {if $info.page == $pages}
                                            <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                        {else}
                                            <option value="{$pages}"> {$pages}</option>
                                        {/if}
                                    {/for}
                                </select>&nbsp;/&nbsp;{$info.total_pages}
                            </form>
                        </td>
                        <td style="width:25%;text-align:right">
                            {if $info.next_page > 0}
                                <a onclick="jrGallery_widget_load_images({$info.next_page},'{$_post.sstr}','{$_post.ids}','false');">{jrCore_icon icon="next"}</a>
                            {/if}
                        </td>
                    </tr>
                </table>
            </div>
        {/if}

    {else}

        <div class="container">
            <div class="row">
                <div class="col12 last">
                    <div class="item">
                        {jrCore_lang module="jrGallery" id="45" default="no gallery images were found"}
                    </div>
                </div>
            </div>
        </div>

    {/if}
</div>