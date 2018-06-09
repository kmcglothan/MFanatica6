<div style="text-align:right;padding:12px;">
    <a onclick="jrLoad('#vc','{$jamroom_url}/video_chart_weekly');"><span class="media_title" style="color:#F7DD4F;">{jrCore_lang  skin=$_conf.jrCore_active_skin id="32" default="Weekly"}</span></a>&nbsp;|
    <a onclick="jrLoad('#vc','{$jamroom_url}/video_chart_monthly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="33" default="Monthly"}</span></a>&nbsp;|
    <a onclick="jrLoad('#vc','{$jamroom_url}/video_chart_yearly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="Yearly"}</span></a>
</div>
{if isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
    {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="7" template="video_chart_row.tpl" require_image="video_image" pagebreak="10" page=$_post.p}
{else}
    {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="7" template="video_chart_row.tpl" pagebreak="10" page=$_post.p}
{/if}
