<div>
    <div style="width:85%;padding-bottom:12px">
        {jrCore_media_player module="jrVideo" field="video_file" item=$item}
    </div>
    <span class="info" style="width:130px;display:inline-block;text-align:right">{jrCore_lang module="jrVideo" id="14" default="video file"}:&nbsp;</span> {$item.video_file_original_name}<br>
    <span class="info" style="width:130px;display:inline-block;text-align:right">{jrCore_lang module="jrVideo" id="26" default="video length"}:&nbsp;</span> {$item.video_file_length}<br>
    <span class="info" style="width:130px;display:inline-block;text-align:right">{jrCore_lang module="jrVideo" id="27" default="video bitrate"}:&nbsp;</span> {$item.video_file_bitrate}kbps
</div>
<br>