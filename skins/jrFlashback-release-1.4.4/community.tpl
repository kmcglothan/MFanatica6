{assign var="selected" value="community"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="26" default="community" assign="page_title"}
{jrCore_page_title title=$page_title}
{assign var="no_inner_div" value="true"}
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
                        {jrCore_list module="jrProfile" order_by="_profile_id random" quota_id=$_conf.jrFlashback_artist_quota search1="profile_active = 1" template="index_artists_row.tpl" limit="4" require_image="profile_image"}
                    {else}
                        {jrCore_list module="jrProfile" order_by="_profile_id random" quota_id=$_conf.jrFlashback_artist_quota search1="profile_active = 1" template="index_artists_row.tpl" limit="4"}
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
                        <div class="col6">
                            <div class="title mr10">{jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="viewed"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</div>
                            <div class="body_3 mr10 mb20">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="community_artists.tpl" require_image="profile_image" pagebreak="5" page=$_post.p}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="community_artists.tpl" pagebreak="5" page=$_post.p}
                                {/if}
                            </div>
                        </div>
                        <div class="col6 last">
                            <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="49" default="played"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="songs"}</div>
                            <div class="body_3 mb20">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrAudio" order_by="audio_file_stream_count NUMERICAL_DESC" template="community_songs.tpl" require_image="audio_image" pagebreak="5" page=$_post.p}
                                {else}
                                    {jrCore_list module="jrAudio" order_by="audio_file_stream_count NUMERICAL_DESC" template="community_songs.tpl" pagebreak="5" page=$_post.p}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="52" default="listeners"}</div>
                            <div class="body_3 mb20">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrFlashback_member_quota`" template="community_profile_row.tpl" limit="12" require_image="profile_image"}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrFlashback_member_quota`" template="community_profile_row.tpl" limit="12"}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</div>
                            <div class="body_3">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrFlashback_artist_quota`" template="community_profile_row.tpl" limit="16" require_image="profile_image"}
                                {else}
                                    {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrFlashback_artist_quota`" template="community_profile_row.tpl" limit="16"}
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
