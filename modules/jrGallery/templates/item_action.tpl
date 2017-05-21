{jrCore_module_url module="jrGallery" assign="murl"}

{if isset($item.action_data.gallery_title_url)}

    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_data.gallery_title_url}/all">{$item.action_data.gallery_title|truncate:70}</a>
    <br>

    {* each image template *}
    {capture assign="imgs"}
    {literal}
        {if isset($_items)}
        {foreach $_items as $_i}
        <a href="{jrGallery_get_gallery_image_url item=$_i}" title="{$_i.gallery_alt_text}">{jrCore_module_function function="jrImage_display" module="jrGallery" type="gallery_image" item_id=$_i._item_id size="icon96" crop="portrait" class="iloutline" alt=$_i.gallery_alt_text width=96 height=96}</a>
        {/foreach}
        {/if}
    {/literal}
    {/capture}

    {if $item.quota_jrGallery_gallery_order != 'off'}
        {jrCore_list module="jrGallery" search1="gallery_title_url = `$item.action_data.gallery_title_url`" search2="_profile_id = `$item.action_data._profile_id`" template=$imgs order_by="gallery_order numerical_asc" limit=4}
    {else}
        {jrCore_list module="jrGallery" search1="gallery_title_url = `$item.action_data.gallery_title_url`" search2="_profile_id = `$item.action_data._profile_id`" template=$imgs order_by="_created desc" limit=4}
    {/if}

{/if}

