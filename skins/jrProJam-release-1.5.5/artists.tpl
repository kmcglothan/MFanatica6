{assign var="selected" value="lists"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
     });
</script>
{if isset($_post.option) && $_post.option == 'by_newest'}
    {assign var="order_by" value="_created desc"}
{else}
    {assign var="order_by" value="profile_name asc"}
{/if}


<div class="container">
    <div class="row">

        <div class="col9">

            <div class="body_1">

                <div class="container">
                    <div class="row">
                        <div class="col12 last">
                            <div id="nav-180">
                                <ul>
                                {if !isset($_post.option) || $_post.option == 'by_name'}
                                    <li id="nav-180-current">{jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}</li>
                                {else}
                                    <li><a onfocus="blur();" href="{$jamroom_url}/artists{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="66" default="alphabetically"}</a></li>
                                {/if}
                                {if isset($_post.option) && $_post.option == 'by_newest'}
                                    <li id="nav-180-current">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</li>
                                {else}
                                    <li><a onfocus="blur();" href="{$jamroom_url}/artists/by_newest{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"}</a></li>
                                {/if}
                                {if isset($_post.option) && $_post.option == 'most_viewed'}
                                    <li id="nav-180-current">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</li>
                                {else}
                                    <li><a onfocus="blur();" href="{$jamroom_url}/artists/most_viewed{if isset($_post._1) && strlen($_post._1) > 0}/{$_post._1}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="artists"}</a></li>
                                {/if}
                                </ul>
                            </div>
                            <div class="clear"></div>
                            <div class="body_3">
                                <div class="br-info capital" style="margin-bottom:20px;">
                                    &raquo;&nbsp;
                                    {* prep our array for looping through and constructing our letter chooser *}
                                    {jrCore_array name="alpha" value=$_conf.jrProJam_letter_alphabet explode="true" separator=","}
                                    {foreach from=$alpha item="char"}
                                        <a href="{$jamroom_url}/artists{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{else}/by_name{/if}/{$char}">{$char}</a>&nbsp;
                                    {/foreach}
                                    &laquo;&nbsp;
                                    <a href="{$jamroom_url}/artists{if isset($_post.option) && strlen($_post.option) > 0}/{$_post.option}{/if}">{jrCore_lang skin=$_conf.jrCore_active_skin id="141" default="Reset"}</a>
                                </div>
                            {if isset($_conf.jrProJam_require_images) && $_conf.jrProJam_require_images == 'on'}
                                {if isset($_post.option) && $_post.option == 'most_viewed'}
                                    {jrCore_list module="jrProfile" chart_field="profile_view_count" chart_days="365" search1="profile_quota_id in `$_conf.jrProJam_artist_quota`" search2="profile_name like `$_post._1`%" template="artists_row.tpl" require_image="profile_image" pagebreak=$_conf.jrProJam_default_artist_pagebreak page=$_post.p}
                                {else}
                                    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJam_artist_quota`" search3="profile_name like `$_post._1`%" template="artists_row.tpl" require_image="profile_image" pagebreak=$_conf.jrProJam_default_artist_pagebreak page=$_post.p}
                                {/if}
                            {else}
                                {if isset($_post.option) && $_post.option == 'most_viewed'}
                                    {jrCore_list module="jrProfile" chart_field="profile_view_count" chart_days="365" search1="profile_quota_id in `$_conf.jrProJam_artist_quota`" search2="profile_name like `$_post._1`%" template="artists_row.tpl" pagebreak=$_conf.jrProJam_default_artist_pagebreak page=$_post.p}
                                {else}
                                    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrProJam_artist_quota`" search3="profile_name like `$_post._1`%" template="artists_row.tpl" pagebreak=$_conf.jrProJam_default_artist_pagebreak page=$_post.p}
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
                {jrCore_include template="side_home.tpl"}
            </div>
        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
