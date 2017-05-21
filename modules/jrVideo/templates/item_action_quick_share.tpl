{jrCore_lang module="jrVideo" id=64 default="video title" assign="ttl"}
{jrCore_lang module="jrVideo" id=14 default="video file" assign="file_button"}
<input type="text" id="video_title" class="form_text" name="video_title" value="" tabindex="1" placeholder="{$ttl|jrCore_entity_string}"><br>
{jrCore_upload_button module="jrVideo" field="video_file" allowed="{$_user.quota_jrVideo_allowed_video_types}" maxsize="{$_user.quota_jrCore_max_upload_size}" multiple="false" upload_text="{$file_button|jrCore_entity_string}" random=$smarty.now}
