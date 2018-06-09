{assign var="selected" value="lists"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="SoundCloud" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
{if isset($_post.option) && $_post.option == 'by_plays'}
    {assign var="order_by" value="soundcloud_stream_count NUMERICAL_DESC"}
{elseif isset($_post.option) && $_post.option == 'by_ratings'}
    {assign var="order_by" value="soundcloud_rating_1_average_count NUMERICAL_DESC"}
{else}
    {assign var="order_by" value="_created desc"}
{/if}


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
                        <div class="col12 last">
                            <div class="body_1">
                                <div class="title">
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="71" default="SoundCloud"}&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    {if !isset($_post.option) || $_post.option == 'by_newest'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;
                                    {else}
                                        <a href="{$jamroom_url}/sound_cloud/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>&nbsp;
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_plays'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}
                                    {else}
                                        <a href="{$jamroom_url}/sound_cloud/by_plays{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="59" default="by plays"}</a>
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_ratings'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}
                                    {else}
                                        <a href="{$jamroom_url}/sound_cloud/by_ratings{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="60" default="by rating"}</a>
                                    {/if}
                                </div>
                                <div class="alpha_title"><span style="font-size: 18px;">&raquo;</span>&nbsp;
                                    {* prep our array for looping through and constructing our letter chooser *}
                                    {jrCore_array name="alpha" value=$_conf.jrSage_letter_alphabet explode="true" separator=","}
                                    {foreach from=$alpha item="char"}
                                        <a href="{$jamroom_url}/sound_cloud{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_newest{/if}/{$char}">{$char}</a>&nbsp;
                                    {/foreach}
                                    <span style="font-size: 18px;">&laquo;</span>&nbsp;
                                    <a href="{$jamroom_url}/sound_cloud{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="105" default="Reset"}</a>
                                </div>
                                <div class="body_3">
                                    {jrCore_list module="jrSoundCloud" order_by=$order_by tpl_dir="jrSage" template="sound_cloud_row.tpl" search="soundcloud_title like `$_post._1`%" pagebreak=$_conf.jrSage_default_pagebreak page=$_post.p}
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
