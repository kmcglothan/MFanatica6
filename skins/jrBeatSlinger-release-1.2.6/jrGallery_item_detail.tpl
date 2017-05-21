{jrCore_module_url module="jrGallery" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrBeatSlinger_breadcrumbs module="jrGallery" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_module_function function="jrPhotoAlbum_button" item_id=$item._item_id photoalbum_for="jrGallery"}
        {jrCore_module_function function="jrGallery_download_button" item=$item}
        {jrCore_module_function function="jrFoxyCart_add_to_cart" module="jrGallery" field="gallery_image" item=$item}
        {jrCore_module_function function="jrFoxyCartBundle_button" module="jrGallery" field="gallery_image" item=$item}
        {jrCore_item_update_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/detail/id=`$item._item_id`" item_id=$item._item_id}
        {jrCore_item_delete_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/delete_image/id=`$item._item_id`" item_id=$item._item_id}
    </div>
</div>

<div class="col8">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrGallery" profile_url=$profile_url}
        <span>{jrCore_lang skin="jrBeatSlinger" id="113" default="Images"} by {$item.profile_name}</span>
        <div class="box_body">
            <div class="wrap detail_section">
                <div class="media">
                    <div class="wrap">
                        {if isset($item.gallery_pending) && $item.gallery_pending == '1'}
                            <div class="page_notice_drop mt10">
                                <div id="page_notice" class="page_notice success">
                                    {jrCore_pending_notice module="jrGallery" item=$item}
                                </div>
                            </div>
                        {/if}

                        <h2 style="margin: 0; padding: 0;">{$item.gallery_title}</h2>

                        {* Gallery Slider *}
                        <div style="padding:12px 0 0;">
                            <div id="gallery_slider">
                                {capture assign="imgs"}
                                {literal}
                                    {if isset($_items)}
                                    {jrCore_module_url module="jrGallery" assign="murl"}
                                    <div class="gallery_slider_prev">
                                        {if $info.prev_page > 0}
                                        <a onclick="jrGallery_slider('{$_items[0]._profile_id}', '{$_items[0].gallery_title_url}', '{$info.prev_page}', '{$info.pagebreak}');">{jrCore_icon icon="previous" size="20"}</a>
                                        {elseif $info.total_pages > 1}
                                        {jrCore_icon icon="cancel" size="20"}
                                        {/if}
                                    </div>
                                    {foreach $_items as $img}
                                    <div class="gallery_slider_img">
                                        <a href="{jrGallery_get_gallery_image_url item=$img}#gallery_img">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$img._item_id size="small" crop="portrait" class="img_shadow" alt=$img.gallery_alt_text width=46 height=46}</a>
                                    </div>
                                    {/foreach}
                                    {if $info.next_page > 0}
                                    <div class="gallery_slider_next">
                                        <a onclick="jrGallery_slider('{$_items[0]._profile_id}','{$_items[0].gallery_title_url}','{$info.next_page}', '{$info.pagebreak}');">{jrCore_icon icon="next" size="20"}</a>
                                    </div>
                                    {/if}
                                    {/if}
                                {/literal}
                                {/capture}
                                {assign var="pb" value="12"}
                                {if jrCore_is_mobile_device()}
                                    {assign var="pb" value="4"}
                                {/if}

                                {if $item.quota_jrGallery_gallery_order != 'off'}
                                    {jrCore_list module="jrGallery" search1="_profile_id = `$item._profile_id`" search2="gallery_title_url = `$item.gallery_title_url`" order_by="gallery_order numerical_asc" pagebreak=$pb page=$smarty.session.jrGallery_page_num template=$imgs}
                                {else}
                                    {jrCore_list module="jrGallery" search1="_profile_id = `$item._profile_id`" search2="gallery_title_url = `$item.gallery_title_url`" order_by="_created desc" pagebreak=$pb page=$smarty.session.jrGallery_page_num template=$imgs}
                                {/if}
                                <div style="clear:both"></div>
                            </div>
                        </div>

                        {if !empty($item.gallery_image_title)}
                            <div class="item center">
                                <h2>{$item.gallery_image_title}</h2>
                            </div>
                        {/if}

                        {* Gallery Image *}
                        <div class="block_image" style="position: relative; margin-top: 12px">
                            {jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="1280" id="gallery_img_src" class="img_scale img_shadow" alt=$item.gallery_alt_text width=false height=false}
                            <div class="lb-nav" style="display: block;">
                                {if $prev._item_id > 0}
                                    <a href="{jrGallery_get_gallery_image_url item=$prev}#gallery_img" class="lb-prev" style="display:block"></a>
                                {/if}
                                {if $next._item_id > 0}
                                    <a href="{jrGallery_get_gallery_image_url item=$next}#gallery_img" class="lb-next" style="display:block"></a>
                                {/if}
                            </div>
                        </div>

                        {if !empty($item.gallery_caption)}
                            <div class="item">
                                {$item.gallery_caption|jrCore_format_string:$item.profile_quota_id}
                            </div>
                        {/if}
                    </div>
                </div>

                {* bring in module features *}
                <div class="action_feedback">
                    {jrBeatSlinger_feedback_buttons module="jrGallery" item=$item}
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
                    {jrCore_item_detail_features module="jrGallery" item=$item}
                </div>
            </div>
        </div>
    </div>
</div>
<div class="col4 last">
    <div class="box">
        {jrBeatSlinger_sort template="icons.tpl" nav_mode="jrGallery" profile_url=$profile_url}
        <div class="box_body">
            <div class="wrap">
                <div id="list" class="sidebar">
                    {jrCore_list
                    module="jrGallery"
                    search="_profile_id != `$item._profile_id`"
                    order_by='_created RANDOM'
                    group_by="gallery_title_url"
                    pagebreak=8
                    template="chart_gallery.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>



