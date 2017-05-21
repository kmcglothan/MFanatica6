{assign var="selected" value="home"}
{assign var="spt" value="home"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#top_singles',core_system_url + '/index_top_singles');
        jrLoad('#newest_artists',core_system_url + '/index_new_artists');
        jrLoad('#top_artists',core_system_url + '/index_top_artists');
         });
</script>

{* FLEX-SLIDER *}
{if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}

{if isset($_conf.jrMediaProLight_slider_profile_ids) && $_conf.jrMediaProLight_slider_profile_ids > 0}
    {assign var="list" value=$_conf.jrMediaProLight_slider_profile_ids}
{else}
    {jrCore_list module="jrProfile" order_by="profile_name random" limit="21" search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="null" skip_triggers=true return_item_id_only=true assign="out"}
    {$list = ","|implode:$out}
{/if}
<div class="container">
    <div class="row">
        <div class="col12 last">

            <div class="slider_container">
                <a onfocus="blur();" href="javascript:void(0);" id="fadeout-carousel"><div class="button-toggle"></div></a>
                <div class="toggle-carousel">

                    <section class="slider">
                        <div id="slider" class="flexslider">
                            <ul class="slides">
                                {if isset($list)}
                                    {if isset($_conf.jrMediaProLight_require_images) && $_conf.jrMediaProLight_require_images == 'on'}
                                        {jrCore_list module="jrProfile" limit="21" quota_id=$_conf.jrMediaProLight_artist_quota search1="_item_id in `$list`" template="index_slider.tpl" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" limit="21" quota_id=$_conf.jrMediaProLight_artist_quota search1="_item_id in `$list`" template="index_slider.tpl"}
                                    {/if}
                                {else}
                                    {if isset($_conf.jrMediaProLight_require_images) && $_conf.jrMediaProLight_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider.tpl" limit="21" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider.tpl" limit="21"}
                                    {/if}
                                {/if}
                            </ul>
                        </div>
                        <div id="carousel" class="flexslider">
                            <ul class="slides">
                                {if isset($list)}
                                    {if isset($_conf.jrMediaProLight_require_images) && $_conf.jrMediaProLight_require_images == 'on'}
                                        {jrCore_list module="jrProfile" quota_id=$_conf.jrMediaProLight_artist_quota search1="_item_id in `$list`" template="index_slider_thumbs.tpl" limit="21" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" quota_id=$_conf.jrMediaProLight_artist_quota search1="_item_id in `$list`" template="index_slider_thumbs.tpl" limit="21"}
                                    {/if}
                                {else}
                                    {if isset($_conf.jrMediaProLight_require_images) && $_conf.jrMediaProLight_require_images == 'on'}
                                        {jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider_thumbs.tpl" limit="21" require_image="profile_image"}
                                    {else}
                                        {jrCore_list module="jrProfile" order_by="_created desc" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_slider_thumbs.tpl" limit="21"}
                                    {/if}
                                {/if}
                            </ul>
                        </div>
                    </section>

                </div>
            </div>

        </div>
    </div>
</div>

{/if}

<div id="content">

<div class="container">

<div class="row">

{* BEGIN LEFT SIDE *}
<div class="col9">
<div class="body_1 mr5">
    <div class="container">

        {* FEATURED ARTIST *}
        <div class="row">

            <div class="col12 last">

                <h1><span style="font-weight:normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured"}</span>&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artist"}</h1>
                <div id="featured_artists" class="mb20">

                    {if isset($_conf.jrMediaProLight_profile_ids) && $_conf.jrMediaProLight_profile_ids > 0}
                        {jrCore_list module="jrProfile" limit="10" search="_item_id in `$_conf.jrMediaProLight_profile_ids`" template="index_featured.tpl" pagebreak="1" page=$_post.p}
                    {elseif isset($_conf.jrMediaProLight_require_images) && $_conf.jrMediaProLight_require_images == 'on'}
                        {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" require_image="profile_image" pagebreak="1" page=$_post.p}
                    {else}
                        {jrCore_list module="jrProfile" order_by="profile_view_count numerical_desc" limit="10" quota_id=$_conf.jrMediaProLight_artist_quota search1="profile_active = 1" search2="profile_jrAudio_item_count > 0" template="index_featured.tpl" pagebreak="1" page=$_post.p}
                    {/if}

                </div>

            </div>

        </div>

        {* TOP SINGLES *}
        <a id="tsingles" name="tsingles"></a>
        <div class="row">

            <div class="col12 last">
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="Top"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="171" default="Singles"}</h1><br>
                <br>
                <div class="top_singles_body mb30 pt20">
                    <div id="top_singles">
                    </div>
                </div>

            </div>

        </div>

        {* NEWEST ARTISTS *}
        <div class="row">

            <div class="col12 last">
                <a id="newartists" name="newartists"></a>
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"}</span> {jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</h1><br>
                <div class="mb30 pt20">
                    <div id="newest_artists">

                    </div>
                </div>

            </div>

        </div>

        {* TOP 10 ARTISTS *}
        <div class="row">

            <div class="col12 last">
                <br>
                <br>
                <br>
                <h1><span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="58" default="top"}</span>&nbsp;10&nbsp;<span style="font-weight: normal;">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</span></h1><br>
                <br>
                <div class="mb30 pt20">
                    <div id="top_artists">

                    </div>
                </div>

            </div>

        </div>

        {* BOTTOM AD *}
        <div class="row">
            <div class="col12 last">

                <div class="center">
                    {if $_conf.jrMediaProLight_ads_off != 'on'}
                        {if isset($_conf.jrMediaProLight_google_ads) && $_conf.jrMediaProLight_google_ads == 'on'}
                            <script type="text/javascript"><!--
                                google_ad_client = "{$_conf.jrMediaProLight_google_id}";
                                google_ad_width = 728;
                                google_ad_height = 90;
                                google_ad_format = "728x90_as";
                                google_ad_type = "text_image";
                                google_ad_channel ="";
                                google_color_border = "CCCCCC";
                                google_color_bg = "CCCCCC";
                                google_color_link = "FF9900";
                                google_color_text = "333333";
                                google_color_url = "333333";
                                //--></script>
                            <script type="text/javascript"
                                    src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
                            </script>
                        {elseif isset($_conf.jrMediaProLight_bottom_ad) && strlen($_conf.jrMediaProLight_bottom_ad) > 0}
                            {$_conf.jrMediaProLight_bottom_ad}
                        {else}
                            <a href="https://www.jamroom.net/" target="_blank">{jrCore_image image="728x90_banner.png" alt="728x90 Ad" title="Get Jamroom5!" class="img_scale" style="max-width:728px;max-height:90px;"}</a>
                        {/if}
                    {/if}
                </div>

            </div>
        </div>

    </div>

</div>

</div>

{* BEGIN RIGHT SIDE *}
<div class="col3 last">
    <div class="body_1">
        {jrCore_include template="side_home.tpl"}
    </div>
</div>

</div>

</div>

{jrCore_include template="footer.tpl"}

