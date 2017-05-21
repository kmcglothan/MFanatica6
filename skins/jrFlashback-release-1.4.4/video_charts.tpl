{assign var="selected" value="charts"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video" assign="page_title1"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts" assign="page_title2"}
{jrCore_page_title title="`$page_title1` `$page_title2`"}
{jrCore_include template="header.tpl"}
<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="body_1">
                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</div>
                <div class="body_3">
                    {if isset($_conf.jrFlashback_profile_ids) && strlen($_conf.jrFlashback_profile_ids) > 0}
                        {jrCore_list module="jrProfile" order_by="_profile_id asc" profile_id=$_conf.jrFlashback_profile_ids template="index_artists_row.tpl" limit="4"}
                    {elseif isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                        {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" quota_id=$_conf.jrFlashback_artist_quota template="index_artists_row.tpl" limit="4" require_image="profile_image"}
                    {else}
                        {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" quota_id=$_conf.jrFlashback_artist_quota template="index_artists_row.tpl" limit="4"}
                    {/if}
                </div>

            </div>
        </div>
    </div>

    <div class="row">

        <div class="col9">
            <div class="body_1 mr5">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="title">
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video"}&nbsp;
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}&nbsp;
                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="1 week"}&nbsp;
                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                <a href="{$jamroom_url}/video_charts_monthly">{jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="1 month"}</a>&nbsp;
                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                <a href="{$jamroom_url}/video_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="63" default="1 year"}</a>&nbsp;
                            </div>
                            <div class="body_3">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="7" tpl_dir="jrFlashback" template="video_chart_row.tpl" require_image="video_image" pagebreak=$_conf.jrFlashback_default_pagebreak page=$_post.p}
                                {else}
                                    {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="7" tpl_dir="jrFlashback" template="video_chart_row.tpl" pagebreak=$_conf.jrFlashback_default_pagebreak page=$_post.p}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div class="col3 last">
            <div class="body_1">
                {jrCore_include template="side.tpl"}
            </div>
        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}
