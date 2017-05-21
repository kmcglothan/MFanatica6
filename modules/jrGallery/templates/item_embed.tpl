{* for embedding a newly created gallery into a wall post, not a single image. *}
{capture name="row_template" assign="template"}
{jrCore_module_url module="jrGallery" assign="murl"}
{literal}
{jrCore_module_url module="jrGallery" assign="murl"}
{foreach $_items as $item}
    <a href="{jrGallery_get_gallery_image_url item=$item}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$item._item_id size="small" crop="auto" class="iloutline" alt=$item.gallery_title}</a>
{/foreach}
<a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all"><span style="margin-left:6px;">{jrCore_icon icon="next"}</span></a>
{/literal}
{/capture}


<div class="embedded_item">
    <div class="row">
        <div class="col12">
            <div class="jr_gallery_row">
                <div>
                    <h2><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.gallery_title_url}/all">{$item.gallery_title}</a></h2>
                    {if !empty($item.gallery_description)}
                        <br>
                        <span class="normal">{$item.gallery_description}</span>
                    {/if}
                </div>
                <div class="mt10" style="padding-top:0">
                    {assign var="limit" value="6"}
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
    </div>
</div>
