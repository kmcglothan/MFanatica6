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
                <div class="body_2">
                    {if isset($_conf.jrSage_profile_ids) && strlen($_conf.jrSage_profile_ids) > 0}
                        {jrCore_list module="jrProfile" order_by="_profile_id asc" profile_id=$_conf.jrSage_profile_ids template="index_artists_row.tpl" limit="4"}
                    {else}
                        {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                            {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" quota_id=$_conf.jrSage_artist_quota template="index_artists_row.tpl" limit="4" require_image="profile_image"}
                        {else}
                            {jrCore_list module="jrProfile" order_by="_profile_id random" search1="profile_active = 1" quota_id=$_conf.jrSage_artist_quota template="index_artists_row.tpl" limit="4"}
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
                        <div class="col6">
                            <div class="body_1">
                                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="47" default="viewed"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</div>
                                <div class="body_2">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="community_artists.tpl" require_image="profile_image" pagebreak="5" page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="community_artists.tpl" pagebreak="5" page=$_post.p}
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="col6 last">
                            <div class="body_1">
                                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="46" default="most"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="49" default="played"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="songs"}</div>
                                <div class="body_2">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {jrCore_list module="jrAudio" order_by="audio_file_stream_count NUMERICAL_DESC" template="community_songs.tpl" require_image="audio_image" pagebreak="5" page=$_post.p}
                                    {else}
                                        {jrCore_list module="jrAudio" order_by="audio_file_stream_count NUMERICAL_DESC" template="community_songs.tpl" pagebreak="5" page=$_post.p}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1">
                                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="52" default="listeners"}</div>
                                <div class="body_2">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrSage_member_quota template="community_profile_row.tpl" limit="12" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrSage_member_quota template="community_profile_row.tpl" limit="12"}
                                    {/if}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div class="body_1">
                                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</div>
                                <div class="body_2">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrSage_artist_quota template="community_profile_row.tpl" limit="16" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="_created desc" search1="profile_active = 1" quota_id=$_conf.jrSage_artist_quota template="community_profile_row.tpl" limit="16"}
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
