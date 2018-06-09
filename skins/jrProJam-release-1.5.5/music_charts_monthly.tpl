{assign var="selected" value="music"}
{assign var="no_inner_div" value="true"}
{if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="monthly" assign="page_title1"}
    {assign var="page_title2" value=$_post.search_string}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music" assign="page_title3"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts" assign="page_title4"}
    {jrCore_page_title title="`$page_title1` `$page_title2` `$page_title3` `$page_title4`"}
{else}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="monthly" assign="page_title1"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music" assign="page_title2"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts" assign="page_title3"}
    {jrCore_page_title title="`$page_title1` `$page_title2` `$page_title3`"}
{/if}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
     });
</script>

<div class="container">

    <div class="row">

        <div class="col9">
            <div class="body_1">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="monthly"}&nbsp;{if isset($_post.search_area) && $_post.search_area == 'audio_genre'}{$_post.search_string}&nbsp;{/if}{jrCore_lang skin=$_conf.jrCore_active_skin id="56" default="music"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}</h2>
                            <div class="body_3">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    <a href="{$jamroom_url}/music_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="1 week"}</a>&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="1 month"}&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    <a href="{$jamroom_url}/music_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="63" default="1 year"}</a>&nbsp;
                                </div>

                                {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
                                    {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" quota_id=$_conf.jrProJam_artist_quota search1="audio_genre like `$_post.search_string`" template="music_chart_row.tpl" require_image="audio_image" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" quota_id=$_conf.jrProJam_artist_quota template="music_chart_row.tpl" require_image="audio_image" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {/if}
                                {else}
                                    {if isset($_post.search_area) && $_post.search_area == 'audio_genre'}
                                        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" quota_id=$_conf.jrProJam_artist_quota search1="audio_genre like `$_post.search_string`" template="music_chart_row.tpl" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="30" quota_id=$_conf.jrProJam_artist_quota template="music_chart_row.tpl" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {/if}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1 ml5">
                {jrCore_include template="side_music_charts.tpl" period="monthly"}
            </div>
        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}
