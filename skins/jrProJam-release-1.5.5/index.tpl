{assign var="selected" value="home"}
{assign var="no_inner_div" value="true"}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrSetActiveBlogs('#bdefault');
        jrSetActiveReviews('#rdefault');
        jrSetActiveEvents('#edefault');
        jrSetActiveSiteBlog('#site_news');
        jrLoad('#newest_artists',core_system_url + '/index_new_artists');
        jrLoad('#newest_members',core_system_url + '/index_new_members');
        jrLoad('#newest_videos',core_system_url + '/index_new_videos');
     });
</script>


<div class="container">

<div class="row">

{* BEGIN RIGHT SIDE *}
<div class="col9">
<div class="body_1">
<div class="row">

{* LATEST NEWS SLIDER *}
    <div class="col7">
        <div class="body_2">
            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}</h2><br>
        </div>
        <div class="body_2">

            <script>
                $(function () {

                    // Slideshow 2
                    $("#slider2").responsiveSlides({
                        auto: true,          // Boolean: Animate automatically, true or false
                        speed: 400,          // Integer: Speed of the transition, in milliseconds
                        timeout: 4000,       // Integer: Time between slide transitions, in milliseconds
                        pager: false,         // Boolean: Show pager, true or false
                        random: false,        // Boolean: Randomize the order of the slides, true or false
                        pause: true,         // Boolean: Pause on hover, true or false
                        maxwidth: 495,       // Integer: Max-width of the slideshow, in pixels
                        namespace: "latest-news" // String: change the default namespace used
                     });

                 });
            </script>
            <div class="callbacks_container">
                <div class="ioutline" style="max-width:495px;margin:0 auto;border:1px solid #333;">
                    <ul id="slider2" class="rslides callbacks" style="height:370px;">
                    {if isset($_conf.jrProJam_news_profile) && $_conf.jrProJam_news_profile > 0}
                        {jrCore_list module="jrBlog" order_by="_created desc" limit="10" search1="_profile_id in `$_conf.jrProJam_news_profile`" search2="blog_category = latest" template="news_slider.tpl"}
                    {else}
                        {jrCore_list module="jrBlog" order_by="_created desc" limit="10" search1="_profile_id = 1" search2="blog_category = latest" template="news_slider.tpl"}
                    {/if}
                    </ul>
                </div>
            </div>
            <br>
            <div class="clear"></div>

        </div>
    </div>

{* WELCOME MESSAGE *}
    <div class="col5 last">
    {capture name="row_template" assign="template"}
        {literal}
            {if isset($_items)}
            {jrCore_module_url module="jrBlog" assign="burl"}
            {foreach from=$_items item="item"}
            <div class="body_2">
                {if jrUser_is_master()}
                <div class="block_config">
                    <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$burl}/{$item._item_id}/{$item.blog_title_url}'">{jrCore_icon icon="gear"}</a>
                </div>
                {/if}
                <h2>{$item.blog_title}</h2><br>
            </div>
            <div class="body_2" style="padding-left:0;padding-right:0;">
                <div class="body_3">
                    <div class="clear" style="height:352px;overflow:auto;">
                        <div style="height:325px;">
                            <span style="font-size:11px;">
                                {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            {/foreach}
            {/if}
        {/literal}
    {/capture}

    {jrCore_list module="jrBlog" order_by="_created desc" limit="1" search1="_user_id = 1" search2="blog_category = welcome" template=$template}
    </div>

</div>

<div class="row">

{* LATEST BLOGS AND NEWS *}
    <div class="col4">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="125" default="Latest Blogs And News"}</h3><br>
        <br>
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="bdefault" class="p_choice_blogs" onclick="jrLoad('#blog_div','{$jamroom_url}/blogs');jrSetActiveBlogs('#bdefault');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="22" default="All"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="featblog" class="p_choice_blogs" onclick="jrLoad('#blog_div','{$jamroom_url}/blogs/featured');jrSetActiveBlogs('#featblog');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="latblog" class="p_choice_blogs" onclick="jrLoad('#blog_div','{$jamroom_url}/blogs/latest');jrSetActiveBlogs('#latblog');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="78" default="Latest"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="excblog" class="p_choice_blogs" onclick="jrLoad('#blog_div','{$jamroom_url}/blogs/exclusive');jrSetActiveBlogs('#excblog');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Exclusive"}</div>
                </td>
            </tr>
        </table>
        <div id="blog_div" class="mb20">
        {jrCore_list module="jrBlog" order_by="_created desc" limit="5" search1="blog_category not_in about,news,welcome,latest,featured,exclusive" template="blogs_row.tpl"}
        </div>
    </div>

{* REVIEWS AND ARTICLES *}
    <div class="col4">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="126" default="Reviews And Articles"}</h3><br>
        <br>
    {if jrCore_module_is_active('jrComment')}
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="rdefault" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/reviews/songs');jrSetActiveReviews('#rdefault');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Songs"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="sreviews" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/reviews/videos');jrSetActiveReviews('#sreviews');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="Videos"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="harticles" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/index_articles');jrSetActiveReviews('#harticles');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="Articles"}</div>
                </td>
            </tr>
        </table>
    {elseif jrUser_is_master()}
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="rdefault" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/reviews/songs');jrSetActiveReviews('#rdefault');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="Songs"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="sreviews" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/reviews/videos');jrSetActiveReviews('#sreviews');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="Videos"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="harticles" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/index_articles');jrSetActiveReviews('#harticles');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="Articles"}</div>
                </td>
            </tr>
        </table>
    {else}
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="rdefault" class="p_choice_reviews" onclick="jrLoad('#reviews_div','{$jamroom_url}/index_articles');jrSetActiveReviews('#rdefault');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="67" default="Articles"}</div>
                </td>
            </tr>
        </table>
    {/if}
        <div id="reviews_div">
        {if jrCore_module_is_active('jrComment')}
            {jrCore_list module="jrAudio" order_by="audio_comment_count NUMERICAL_DESC" limit="5" template="reviews_row.tpl"}
        {elseif jrUser_is_master()}
            <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
                <div class="highlight-txt center">
                    Install and Activate the<br>
                    jrComment Module!<br>
                    <a href="{$jamroom_url}/core/admin/global">Control Panel</a>
                </div>
            </div>
        {else}
            {capture name="row_template" assign="index_articles_row"}
                {literal}
                    {if isset($_items)}
                    {foreach from=$_items item="item"}
                    <div class="body_5 page" style="padding:5px; margin-bottom:5px; margin-right:10px;">
                        <h3 style="font-weight:normal;">
                            {if $item.page_location == '0'}
                            <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}">{if strlen($item.page_title) > 35}{$item.page_title|truncate:35:"...":false}{else}{$item.page_title}{/if}</a></h3>
                        {else}
                        <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}">{if strlen($item.page_title) > 35}{$item.page_title|truncate:35:"...":false}{else}{$item.page_title}{/if}</a></h3>
                        {/if}
                        <div style="font-size:12px;">{$item._created|jrCore_format_time}</div>
                        <div style="font-size:11px;">
                            <span class="highlight-txt">By:</span>&nbsp;<span class="capital">{$item.profile_name}</span>
                            {if jrCore_module_is_active('jrComment')}
                            <br>
                            <div class="float-right" style="padding-right:5px;">
                                {if $item.page_location == '0'}
                                <a href="{$jamroom_url}/page/{$item._item_id}/{$item.page_title_url}"><span class="highlight-txt capital"> {jrCore_lang module="jrBlog" id="27" default="comments"}:</span></a>  {$item.blog_comment_count|default:0}<br>
                                {else}
                                <a href="{jrProfile_item_url module="jrPage" profile_url=$item.profile_url item_id=$item._item_id title=$item.page_title}#comments"><span class="highlight-txt capital"> {jrCore_lang module="jrBlog" id="27" default="comments"}:</span></a>  {$item.page_comment_count|default:0}<br>
                                {/if}
                            </div>
                            <div class="clear"></div>
                            <br>
                            {/if}
                        </div>
                    </div>
                    {/foreach}
                    {/if}
                {/literal}
            {/capture}

            {jrCore_list module="jrPage" order_by="_created desc" limit="5" template=$index_articles_row}
        {/if}
        </div>
    </div>

{* EVENTS CALENDAR *}
    <div class="col4 last">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="127" default="Events Calendar"}</h3><br>
        <br>
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="edefault" class="p_choice_events" onclick="jrLoad('#event_div','{$jamroom_url}/index_events/upcoming');jrSetActiveEvents('#edefault');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="114" default="Scheduled"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="newevent" class="p_choice_events" onclick="jrLoad('#event_div','{$jamroom_url}/index_events/newest');jrSetActiveEvents('#newevent');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="featevent" class="p_choice_events" onclick="jrLoad('#event_div','{$jamroom_url}/index_events/featured');jrSetActiveEvents('#featevent');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="21" default="Featured"}</div>
                </td>
            </tr>
        </table>
        <div id="event_div">
        {* ROW TEMPLATE *}
        {capture name="row_template" assign="index_event_template"}
            {literal}
                {if isset($_items)}
                {jrCore_module_url module="jrEvent" assign="murl"}
                {foreach from=$_items item="item"}
                <div class="body_5 page" style="padding:5px;margin-bottom:5px;margin-right:0;">
                    <div style="display:table;">
                        <div style="display:table-row;height:40px;">
                            <div style="display:table-cell;text-align:center;vertical-align:top;">
                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="medium" crop="auto" width="35" height="35" alt=$item.event_title title=$item.event_title class="iloutline" style="max-width:140px;"}</a>
                            </div>
                            <div style="display:table-cell;width:99%;text-align:left;vertical-align:top;padding-left:5px;">
                                <h3 style="font-weight:normal;">
                                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{if strlen($item.event_title) > 30}{$item.event_title|truncate:30:"...":false}{else}{$item.event_title}{/if}</a>
                                </h3>
                                <div style="font-size:12px;">{$item.event_date|jrCore_date_format}</div>
                                <div style="font-size:11px;"><span class="highlight-txt">{$item.event_location|truncate:30:"...":false}</span></div>
                                {if jrCore_module_is_active('jrComment')}
                                    <br>
                                    <div class="float-right" style="padding-right:5px;">
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.event_comment_count|default:0}</a>
                                    </div>
                                    <div class="clear"></div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
                {/foreach}
                {/if}
            {/literal}
        {/capture}

        {* EVENT LIST FUNCTION *}
        {jrCore_list module="jrEvent" search="event_date >= `$smarty.now`" order_by="event_date NUMERICAL_ASC" limit="5" template=$index_event_template}
        </div>
    </div>

</div>

<div class="row">

<a id="mnews" name="mnews"></a>
{* SITE NEWS, BLOGS AND ABOUT *}
    <div class="col12 last">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="116" default="Site News"}</h3><br>
        <br>
        <table class="menu_tab">
            <tr>
                <td>
                    <div id="site_news" class="p_choice_site_blogs" onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news');jrSetActiveSiteBlog('#site_news');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="116" default="Site News"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="site_blog" class="p_choice_site_blogs" onclick="jrLoad('#site_news_div','{$jamroom_url}/site_blog');jrSetActiveSiteBlog('#site_blog');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="117" default="Site Blog"}</div>
                </td>
                <td class="spacer">&nbsp;</td>
                <td>
                    <div id="about" class="p_choice_site_blogs" onclick="jrLoad('#site_news_div','{$jamroom_url}/about');jrSetActiveSiteBlog('#about');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');" style="font-size:10px;font-weight:bold;padding-left:4px;padding-right:4px;">{jrCore_lang skin=$_conf.jrCore_active_skin id="118" default="About"}</div>
                </td>
            </tr>
        </table>
        <div id="site_news_div" class="body_3" style="margin-bottom:40px;">
            <div style="height:580px;overflow:auto;">
                {* ROW TEMPLATE *}
                {capture name="row_template" assign="site_news_template"}
                    {literal}
                        {if isset($_items)}
                        {jrCore_module_url module="jrBlog" assign="murl"}
                        {foreach from=$_items item="item"}
                        <div style="padding:10px;">
                            <div class="br-info" style="margin-bottom:20px;">
                                <div class="blog-div">
                                    <span class="blog-user capital"> By {$item.profile_name}</span>
                                </div>
                                <div class="blog-div">
                                    <span class="blog-date"> {$item.blog_publish_date|jrCore_format_time}</span>
                                </div>
                                <div class="blog-div">
                                    <span class="blog-tag capital"> Tag: {$item.blog_category}</span>
                                </div>
                                {if jrCore_module_is_active('jrComment')}
                                <div class="blog-div">
                                <span class="blog-replies">
                                    {if $item.profile_id == '1'}
                                        <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                    {else}
                                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                    {/if}
                                </span>
                                </div>
                                {/if}
                                {if jrUser_is_master()}
                                <div class="float-right">
                                    <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}'">{jrCore_icon icon="gear"}</a>
                                </div>
                                {/if}
                                <div class="clear"></div>
                            </div>
                            <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3>
                            <div class="blog-text">
                                {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                                {jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="large" alt=$item.blog_title width="128" height="128" crop="auto" class="iloutline img_shadow" style="float:left;margin-right:8px;margin_bottom:8px;"}
                                {/if}
                                {$item.blog_text|jrCore_format_string:$item.profile_quota_id|nl2br|jrBlog_readmore}
                            </div>
                        </div>
                        {/foreach}
                        {if $info.total_pages > 1}
                        <div class="block">
                            <table style="width:100%;">
                                <tr>

                                    <td class="body_5 page" style="width:25%;text-align:center;">
                                        {if isset($info.prev_page) && $info.prev_page > 0}
                                        <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.prev_page}');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');"><span class="button-arrow-previous">&nbsp;</span></a>
                                        {else}
                                        <span class="button-arrow-previous-off">&nbsp;</span>
                                        {/if}
                                    </td>

                                    <td class="body_5" style="width:50%;text-align:center;">
                                        {if $info.total_pages <= 1 || $info.total_pages > 500}
                                        {$info.page} &nbsp;/ {$info.total_pages}
                                        {else}
                                        <form name="form" method="post" action="_self">
                                            <select name="pagenum" class="form_select" style="width:60px;" onchange="var sel=this.form.pagenum.options[this.form.pagenum.selectedIndex].value;jrLoad('#site_news_div','{$jamroom_url}/site_news/p=' +sel);$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');">
                                                {for $pages=1 to $info.total_pages}
                                                {if $info.page == $pages}
                                                <option value="{$info.this_page}" selected="selected"> {$info.this_page}</option>
                                                {else}
                                                <option value="{$pages}"> {$pages}</option>
                                                {/if}
                                                {/for}
                                            </select>&nbsp;/&nbsp;{$info.total_pages}
                                        </form>
                                        {/if}
                                    </td>

                                    <td class="body_5 page" style="width:25%;text-align:center;">
                                        {if isset($info.next_page) && $info.next_page > 1}
                                        <a onclick="jrLoad('#site_news_div','{$jamroom_url}/site_news/p={$info.next_page}');$('html, body').animate({ scrollTop: $('#mnews').offset().top -100 }, 'slow');"><span class="button-arrow-next">&nbsp;</span></a>
                                        {else}
                                        <span class="button-arrow-next-off">&nbsp;</span>
                                        {/if}
                                    </td>

                                </tr>
                            </table>
                        </div>
                        {/if}
                        {/if}
                    {/literal}
                {/capture}

                {* SITE NEWS FUNCTION *}
                {if isset($_conf.jrProJam_news_profile) && $_conf.jrProJam_news_profile > 0}
                    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id in `$_conf.jrProJam_news_profile`" search2="blog_category = news" template=$site_news_template pagebreak=$_conf.jrProJam_index_news_limit page=$_post.p}
                {else}
                    {jrCore_list module="jrBlog" order_by="_created desc" search1="_profile_id = 1" search2="blog_category = news" template=$site_news_template pagebreak=$_conf.jrProJam_index_news_limit page=$_post.p}
                {/if}
            </div>
        </div>
    </div>

</div>
<div class="row">

<a id="nartists" name="nartists"></a>
{* NEWEST ARTISTS *}
    <div class="col12 last">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="Artists"}</h3><br>
        <br>
        <div class="newest_body_3 mb30 pt20">
            <div id="newest_artists">

            </div>
        </div>
    </div>

</div>

<div class="row">

<a id="nmembers" name="nmembers"></a>
{* NEWEST MEMBERS *}
    <div class="col12 last">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="40" default="Members"}</h3><br>
        <br>
        <div class="newest_body_2b mb30 pt20">
            <div id="newest_members">
            </div>
        </div>
    </div>

</div>

<div class="row">

<a id="nvideos" name="nvideos"></a>
{* NEWEST VIDEOS *}
    <div class="col12 last">
        <h3>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="Newest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="Videos"}</h3><br>
        <br>
        <div class="newest_body_3 mb30 pt20">
            <div id="newest_videos">

            </div>
        </div>
    </div>

</div>

</div>

</div>

{* BEGIN LEFT SIDE *}
<div class="col3 last">
    <div class="body_1 ml5">
        {jrCore_include template="side_home.tpl"}
    </div>
</div>

</div>

</div>

{jrCore_include template="footer.tpl"}

