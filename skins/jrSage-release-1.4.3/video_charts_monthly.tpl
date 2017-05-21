{assign var="selected" value="charts"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}
<div class="container">

    <div class="row">

        <div class="col12 last">

            <div class="body_1">

                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</div>
                <div class="body_2">
                    {if isset($_conf.jrSage_profile_ids) && strlen($_conf.jrSage_profile_ids) > 0}
                        {jrCore_list module="jrProfile" order_by="_profile_id asc" profile_id=$_conf.jrSage_profile_ids template="index_artists_row.tpl" limit="4"}
                    {else}
                        {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                            {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrSage_artist_quota`" template="index_artists_row.tpl" limit="4" require_image="profile_image"}
                        {else}
                            {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrSage_artist_quota`" template="index_artists_row.tpl" limit="4"}
                        {/if}
                    {/if}
                </div>

            </div>

        </div>

    </div>

    <div class="row">

        <div class="col9">
            <div class="mr5">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1">
                                <div class="title">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video"}&nbsp;
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="27" default="charts"}&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    <a href="{$jamroom_url}/video_charts">{jrCore_lang skin=$_conf.jrCore_active_skin id="61" default="1 week"}</a>&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="62" default="1 month"}&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    <a href="{$jamroom_url}/video_charts_yearly">{jrCore_lang skin=$_conf.jrCore_active_skin id="63" default="1 year"}</a>&nbsp;
                                </div>
                                <div class="body_3">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="30" tpl_dir="jrSage" template="video_chart_row.tpl" require_image="video_image" pagebreak=$_conf.jrSage_default_pagebreak page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrVideo" chart_field="video_file_stream_count" chart_days="30" tpl_dir="jrSage" template="video_chart_row.tpl" pagebreak=$_conf.jrSage_default_pagebreak page=$_post.p}
                                    {/if}
                                </div>
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
