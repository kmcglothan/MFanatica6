{assign var="selected" value="lists"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
{if isset($_post.option) && $_post.option == 'by_plays'}
    {assign var="order_by" value="video_file_stream_count NUMERICAL_DESC"}
{elseif isset($_post.option) && $_post.option == 'by_ratings'}
    {assign var="order_by" value="video_rating_1_average_count NUMERICAL_DESC"}
{else}
    {assign var="order_by" value="_created desc"}
{/if}
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
                                {jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}&nbsp;
                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                {if !isset($_post.option) || $_post.option == 'by_newest'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;
                                {else}
                                    <a href="{$jamroom_url}/videos/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>&nbsp;
                                {/if}

                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                {if isset($_post.option) && $_post.option == 'by_plays'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}
                                {else}
                                    <a href="{$jamroom_url}/videos/by_plays{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}</a>
                                {/if}

                                <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                {if isset($_post.option) && $_post.option == 'by_ratings'}
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}
                                {else}
                                    <a href="{$jamroom_url}/videos/by_ratings{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}</a>
                                {/if}
                            </div>
                            <div class="alpha_title">&raquo;&nbsp;
                                {* prep our array for looping through and constructing our letter chooser *}
                                {jrCore_array name="alpha" value=$_conf.jrFlashback_letter_alphabet explode="true" separator=","}
                                {foreach from=$alpha item="char"}
                                    <a href="{$jamroom_url}/videos{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_newest{/if}/{$char}">{$char}</a>&nbsp;
                                {/foreach}
                                &laquo;&nbsp;
                                <a href="{$jamroom_url}/videos{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="105" default="Reset"}</a>
                            </div>
                            <div class="body_3">
                                {if isset($_conf.jrFlashback_require_images) && $_conf.jrFlashback_require_images == 'on'}
                                    {jrCore_list module="jrVideo" order_by=$order_by tpl_dir="jrFlashback" template="videos_row.tpl" search="video_title like `$_post._1`%" require_image="video_image" pagebreak=$_conf.jrFlashback_default_pagebreak page=$_post.p}
                                {else}
                                    {jrCore_list module="jrVideo" order_by=$order_by tpl_dir="jrFlashback" template="videos_row.tpl" search="video_title like `$_post._1`%" pagebreak=$_conf.jrFlashback_default_pagebreak page=$_post.p}
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
