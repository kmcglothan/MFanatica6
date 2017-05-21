{jrCore_lang module="jrGallery" id=3 default="gallery title" assign="ttl"}
{jrCore_lang module="jrGallery" id=40 default="select images to upload" assign="file_button"}
<input type="text" id="gallery_title" class="form_text" name="gallery_title" value="" tabindex="1" placeholder="{$ttl|jrCore_entity_string}"><br>
{jrCore_upload_button module="jrGallery" field="gallery_image" allowed="{$_user.quota_jrImage_allowed_image_types}" maxsize="{$_user.quota_jrImage_max_image_size}" multiple="true" upload_text="{$file_button|jrCore_entity_string}" random=$smarty.now}
