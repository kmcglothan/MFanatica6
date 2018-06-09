{assign var="selected" value="home"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">

        <div class="col12 last">

            <div class="body_1">

                <div class="title">
                    {jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"} {jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}
                </div>
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
            <div class="body_1 mr5">

                {if jrCore_module_is_active('jrRecommend')}

                    <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="31" default="find new music"}</div>
                    <div class="body_1 mb20">
                        <div class="container">
                            <div class="row">
                                <div class="col2">
                                    <div class="center middle">
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="31" default="find new music" assign="rec_img_title"}
                                        {jrCore_image image="recommend.png" class="jlogo" alt=$_conf.jrCore_system_name title=$rec_img_title style="max-width:96;max-height:96"}
                                    </div>
                                </div>
                                <div class="col10 last">
                                    <div class="p10">
                                        <b>{jrCore_lang skin=$_conf.jrCore_active_skin id="32" default="Enter an Artist you would like to find music similar to"}:</b><br /><br />
                                        <div class="p5">
                                            {jrRecommend_form class="form_text" value="{jrCore_lang skin=$_conf.jrCore_active_skin id="24" default="search"}" submit_value="{jrCore_lang skin=$_conf.jrCore_active_skin id="31" default="find new music"}" template="recommend_form.tpl" style="max-width:260px;"}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                {/if}
                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="33" default="top blogs"} <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span> <a href="{$jamroom_url}/blogs">{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="archive"}</a></div>
                <div class="body_3">
                    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id in `$_conf.jrSage_index_blog_profile`" template="index_blogs.tpl" limit=$_conf.jrSage_index_blog_limit}
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

