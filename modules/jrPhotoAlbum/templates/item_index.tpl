{jrCore_module_url module="jrPhotoAlbum" assign="murl"}
<div class="block">
    <div class="title">
        <div class="block_config">
            {jrCore_item_index_buttons module="jrPhotoAlbum" profile_id=$_profile_id}
        </div>
        <h1>{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}</h1>
        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrPhotoAlbum" id="11" default="photo album"}</a>
        </div>
    </div>
    <div class="block_content">
        {jrCore_list module="jrPhotoAlbum" profile_id=$_profile_id order_by="photoalbum_display_order numerical_asc" pagebreak="6" page=$_post.p pager=true}
    </div>
</div>
