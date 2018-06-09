{assign var="selected" value="videos"}
{assign var="no_inner_div" value="true"}
{if isset($_post.search_area) && $_post.search_area == 'video_category'}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="yearly" assign="page_title1"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video" assign="page_title2"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts" assign="page_title3"}
    {assign var="page_title4" value=$_post.search_string}
    {jrCore_page_title title="`$page_title1` `$page_title2` `$page_title3` - `$page_title4`"}
{else}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="54" default="monthly" assign="page_title1"}
    {jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="Video" assign="page_title2"}
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
                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="55" default="yearly"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}{if isset($_post.search_area) && $_post.search_area == 'video_category'}&nbsp;-&nbsp;{$_post.search_string}{/if}</h2>
                            <div class="body_3">
                                <div class="br-info capital" style="margin-bottom:10px;">
                                    <a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="1 week"}</a>&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    <a href="{$jamroom_url}/video_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="1 month"}</a>&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="63" default="1 year"}&nbsp;
                                </div>
                                {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
                                    {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="365" search="video_category like `$_post.search_string`" template="video_chart_row.tpl" require_image="video_image" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="365" template="video_chart_row.tpl" require_image="video_image" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {/if}
                                {else}
                                    {if isset($_post.search_area) && $_post.search_area == 'video_category'}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="365" search="video_category like `$_post.search_string`" template="video_chart_row.tpl" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="365" template="video_chart_row.tpl" pagebreak=$_conf.jrProJam_default_pagebreak page=$_post.p}
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
                {jrCore_include template="side_videos_chart.tpl" period="yearly"}
            </div>
        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}
