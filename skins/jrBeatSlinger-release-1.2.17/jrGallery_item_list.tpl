{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($_items)}

    {if $_post.module == 'jrTags' || $_post.module == 'jrSearch' || isset($_post['ss'])}

        <div class="container">
            {foreach from=$_items item="item"}
            {if $item@iteration === 1 || ($item@iteration % 4) === 1}
                <div class="row">
            {/if}
            <div class="col3{if ($item@iteration % 4) === 0} last{/if} relative">
                <div id="p{$item._item_id}" class="p5 center">
                    <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="large" crop="auto" class="iloutline img_scale" alt=$item.gallery_image_name title=$item.gallery_alt_text}</a><br>
                    <a href="{jrGallery_get_gallery_image_url item=$item}" title="{$item.gallery_alt_text}">
                    {if isset($item.gallery_image_title)}
                        {$item.gallery_image_title|truncate:25:"...":false}
                    {else}
                        {$item.gallery_image_name|truncate:25:"...":true}
                    {/if}
                    </a><br><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a><br><a href="{$jamroom_url}/{$item.profile_url}" style="margin-bottom:10px">@{$item.profile_url}</a>

                    {if (jrCore_module_is_active('jrPhotoAlbum')) }
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

            </div>
            {if ($item@iteration % 4) === 0 || $item@last}
                </div>
            {/if}
            {/foreach}
        </div>

    {else}

        {capture name="row_template" assign="template"}
        {literal}
        {jrCore_module_url module="jrGallery" assign="murl"}
        {foreach $_items as $item}
            <a href="{jrGallery_get_gallery_image_url item=$item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="small" crop="auto" class="iloutline" alt=$item.gallery_title}</a>
        {/foreach}
        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
        {/literal}
        {/capture}

        {foreach $_items as $item}
            {if !isset($item.gallery_title_url) || strlen($item.gallery_title_url) == 0}
                {continue}
            {/if}

            <div class="item">
                <div class="container">
                    <div class="row">
                        <div class="col10">
                            <div class="jr_gallery_row">
                                <div>
                                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a></h2>
                                    {if !empty($item.gallery_description)}
                                        <br>
                                        <span class="normal">{$item.gallery_description}</span>
                                    {/if}
                                </div>
                                <div class="mt10" style="padding-top:0">
                                    {assign var="limit" value="10"}
                                    {if jrCore_is_mobile_device()}
                                        {assign var="limit" value="5"}
                                    {/if}

                                    {if $item.quota_jrGallery_gallery_order != 'off'}
                                        {jrCore_list module="jrGallery" profile_id=$item._profile_id search1="gallery_title_url = `$item.gallery_title_url`" template=$template order_by="gallery_order numerical_asc" exclude_jrUser_keys="true" exclude_jrProfile_quota_keys="true" limit=$limit}
                                    {else}
                                        {jrCore_list module="jrGallery" profile_id=$item._profile_id search1="gallery_title_url = `$item.gallery_title_url`" template=$template order_by="_created desc" exclude_jrUser_keys="true" exclude_jrProfile_quota_keys="true" limit=$limit}
                                    {/if}

                                </div>
                            </div>
                        </div>
                        <div class="col2 last">
                            <div class="block_config">

                                {jrCore_item_update_button module="jrGallery" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrGallery" profile_id=$item._profile_id action="`$murl`/delete_save/`$item.profile_url`/`$item.gallery_title_url`"}

                            </div>
                        </div>
                    </div>

                </div>
            </div>
        {/foreach}

    {/if}
{else}
    {jrCore_include template="no_items.tpl"}
{/if}
