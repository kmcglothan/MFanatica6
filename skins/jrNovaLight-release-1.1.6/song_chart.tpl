{assign var="selected" value="charts"}
{assign var="no_inner_div" value="true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="28" assign="page_title1"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="27" assign="page_title2"}
{jrCore_page_title title="`$page_title1` `$page_title2`"}
{jrCore_include template="header.tpl"}

<a id="schart" name="schart"></a>
<div class="menu_tab">
    <div class="p_choice_active" style="white-space:nowrap;" onclick="jrCore_window_location('{$jamroom_url}/song_chart');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="28" default="Song"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="27" default="charts"}</div>
    <div class="p_choice" style="white-space:nowrap;" onclick="jrCore_window_location('{$jamroom_url}/video_chart');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="29" default="video"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="27" default="charts"}</div>
    <div class="clear"></div>
</div>
<div class="inner">
    <div id="sc">
        <div style="text-align:right;padding:12px;">
            <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_weekly');"><span class="media_title" style="color:#999999;">{jrCore_lang  skin=$_conf.jrCore_active_skin id="32" default="Weekly"}</span></a>&nbsp;|
            <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_monthly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="33" default="Monthly"}</span></a>&nbsp;|
            <a onclick="jrLoad('#sc','{$jamroom_url}/song_chart_yearly');"><span class="media_title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="Yearly"}</span></a>
        </div>
    {if isset($_conf.jrNovaLight_require_images) && $_conf.jrNovaLight_require_images == 'on'}
        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="7" template="song_chart_row.tpl" require_image="audio_image" pagebreak="10" page=$_post.p}
    {else}
        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="7" template="song_chart_row.tpl" pagebreak="10" page=$_post.p}
    {/if}
    </div>
</div>

{jrCore_include template="footer.tpl"}
