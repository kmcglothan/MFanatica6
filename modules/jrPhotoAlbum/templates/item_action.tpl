{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
<span class="action_item_title">
    {if $item.action_mode == 'create'}
        {jrCore_lang module="jrPhotoAlbum" id=12 default="Created a new Photo Album"}:
    {else}
        {jrCore_lang module="jrPhotoAlbum" id=13 default="Updated a Photo Album"}:
    {/if}
    <br>
    <a href="{$jamroom_url}/{$item.action_data.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.photoalbum_title_url}" title="{$item.action_data.photoalbum_title|jrCore_entity_string}">{$item.action_data.photoalbum_title|truncate:60:"..."}</a>
</span>
