<div style="text-align:right;padding:12px;">
    <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_weekly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="32" default="Weekly"}</span></a>&nbsp;|
    <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_monthly');"><span class="media_title" style="color:#999999;">{jrCore_lang  skin=$_conf.jrCore_active_skin id="33" default="Monthly"}</span></a>&nbsp;|
    <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_yearly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="Yearly"}</span></a>
</div>
{if isset($_conf.jrNovaLight_require_images) && $_conf.jrNovaLight_require_images == 'on'}
    {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" template="song_chart_row.tpl" require_image="audio_image" pagebreak="10" page=$_post.p}
{else}
    {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" template="song_chart_row.tpl" pagebreak="10" page=$_post.p}
{/if}
