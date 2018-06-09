{jrCore_lang module="jrAudio" id=67 default="audio title" assign="ttl"}
{jrCore_lang module="jrAudio" id=14 default="audio file" assign="file_button"}
{jrCore_lang module="jrAudio" id=16 default="cover image" assign="image_button"}
<input type="text" id="audio_title" class="form_text" name="audio_title" value="" tabindex="{jrCore_next_tabindex}" placeholder="{$ttl|jrCore_entity_string}"><br>
{jrCore_upload_button module="jrAudio" field="audio_file" allowed="{$_user.quota_jrAudio_allowed_audio_types}" maxsize="{$_user.quota_jrCore_max_upload_size}" multiple="false" upload_text="{$file_button|jrCore_entity_string}" random=$smarty.now}
{jrCore_upload_button module="jrAudio" field="audio_image" allowed="{$_user.quota_jrImage_allowed_image_types}" maxsize="{$_user.quota_jrImage_max_image_size}" multiple="false" upload_text="{$image_button|jrCore_entity_string}" random=$smarty.now}

