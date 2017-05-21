{assign var="selected" value="blogs"}
{assign var="no_inner_div" value="true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="10" default="Blog" assign="page_title1"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="Archive" assign="page_title2"}
{jrCore_page_title title="`$page_title1` `$page_title2`"}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">
        <div class="col12 last">

            <div class="body_1">
                <div class="title">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="featured"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="12" default="profiles"}</div>
                <div class="body_3">
                    {if isset($_conf.jrFlashback_profile_ids) && strlen($_conf.jrFlashback_profile_ids) > 0}
                        {jrCore_list module="jrProfile" order_by="_profile_id numerical_asc" profile_id=$_conf.jrFlashback_profile_ids template="index_artists_row.tpl" limit="4"}
                    {elseif isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                        {jrCore_list module="jrProfile" order_by="_profile_id numerical_desc" quota_id=$_conf.jrFlashback_artist_quota search1="profile_active = 1" template="index_artists_row.tpl" limit="4" require_image="profile_image"}
                    {else}
                        {jrCore_list module="jrProfile" order_by="_profile_id numerical_desc random" quota_id=$_conf.jrFlashback_artist_quota search1="profile_active = 1" template="index_artists_row.tpl" limit="4"}
                    {/if}
                </div>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col9">
            <div class="body_1 mr5">

                <div class="title">{jrCore_lang  skin=$_conf.jrCore_active_skin id="34" default="archive"} <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span> <a href="{$jamroom_url}">{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="home"}</a></div>
                <div class="body_3">
                    {jrCore_list module="jrBlog" template="index_blogs.tpl" pagebreak=$_conf.jrFlashback_default_blog_pagebreak page=$_post.p}
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

