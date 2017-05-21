{assign var="selected" value="lists"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
{if isset($_post.option) && $_post.option == 'by_newest'}
    {assign var="order_by" value="_created desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
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
                                    {jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}&nbsp;
                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;
                                    {if !isset($_post.option) || $_post.option == 'by_name'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}&nbsp;
                                    {else}
                                        <a href="{$jamroom_url}/artists/by_name{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}</a>&nbsp;
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'by_newest'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}
                                    {else}
                                        <a href="{$jamroom_url}/artists/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a>
                                    {/if}

                                    <span class="separator">&nbsp;&nbsp;&nbsp;&nbsp;</span>&nbsp;

                                    {if isset($_post.option) && $_post.option == 'most_viewed'}
                                        {jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}
                                    {else}
                                        <a href="{$jamroom_url}/artists/most_viewed{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a>
                                    {/if}
                                </div>
                                <div class="alpha_title">
                                    <div class="container">
                                        <div class="row">
                                            <div class="col12 last">
                                                <span style="font-size: 18px;">&raquo;</span>&nbsp;
                                                {* prep our array for looping through and constructing our letter chooser *}
                                                {jrCore_array name="alpha" value=$_conf.jrSage_letter_alphabet explode="true" separator=","}
                                                {foreach from=$alpha item="char"}
                                                    <a href="{$jamroom_url}/artists{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_name{/if}/{$char}">{$char}</a>&nbsp;
                                                {/foreach}
                                                <span style="font-size: 18px;">&laquo;</span>&nbsp;
                                                <a href="{$jamroom_url}/artists{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="105" default="Reset"}</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="body_3">
                                    {if isset($_conf.jrSage_require_images) && $_conf.jrSage_require_images == 'on'}
                                        {if isset($_post.option) && $_post.option == 'most_viewed'}
                                            {jrCore_list module="jrProfile" chart_field="profile_view_count" chart_days="365" quota_id=$_conf.jrSage_artist_quota search2="profile_name like `$_post._1`%" template="artists_row.tpl" require_image="profile_image" pagebreak=$_conf.jrSage_default_artist_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrProfile" order_by=$order_by quota_id=$_conf.jrSage_artist_quota search1="profile_active = 1" search2="profile_name like `$_post._1`%" template="artists_row.tpl" require_image="profile_image" pagebreak=$_conf.jrSage_default_artist_pagebreak page=$_post.p}
                                        {/if}
                                    {else}
                                        {if isset($_post.option) && $_post.option == 'most_viewed'}
                                            {jrCore_list module="jrProfile" chart_field="profile_view_count" chart_days="365" quota_id=$_conf.jrSage_artist_quota search1="profile_name like `$_post._1`%" tpl_dir="jrSage" template="artists_row.tpl" pagebreak=$_conf.jrSage_default_artist_pagebreak page=$_post.p}
                                        {else}
                                            {jrCore_list module="jrProfile" order_by=$order_by quota_id=$_conf.jrSage_artist_quota search1="profile_active = 1" search2="profile_name like `$_post._1`%" tpl_dir="jrSage" template="artists_row.tpl" pagebreak=$_conf.jrSage_default_artist_pagebreak page=$_post.p}
                                        {/if}
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
