{assign var="selected" value="ban"}
{assign var="no_inner_div" value="true"}
{jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}
<script type="text/javascript">
    $(document).ready(function(){
        jrSetActive('#default');
        jrLoad('#news_listing',core_system_url + '/news_list');
     });
</script>

<div class="container">
    <div class="row">

        <div class="col3">
            <div class="body_1">
            {jrCore_include template="side_news.tpl"}
            </div>
        </div>

        <div class="col9 last">

            <div class="body_1 mr5">

                <div class="container">
                    <div class="row">
                    {* LATEST NEWS SLIDER *}
                        <div class="col8">

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
                                    <div class="ioutline" style="max-width:495px;margin:0 auto;">
                                        <ul id="slider2" class="rslides callbacks" style="height:370px;">
                                        {jrCore_list module="jrBlog" order_by="_created desc" limit="10" search1="_user_id = 1" search2="blog_category = latest" template="news_slider.tpl"}
                                        </ul>
                                    </div>
                                </div>
                                <br>
                                <div class="clear"></div>

                            </div>

                            {* LATEST NEWS *}
                            <a name="newslist" id="newslist"></a>
                            <div id="news_listing">

                            </div>

                        </div>

                    {* EXCLUSIVE NEWS *}
                        <div class="col4 last">

                            {capture name="row_template" assign="exclusive_news"}
                                {literal}
                                    {jrCore_module_url module="jrBlog" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_2">
                                            <div class="block_config">
                                                <a onclick="window.location='{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.blog_title_url}'">{jrCore_icon icon="gear" size="18"}</a>
                                            </div>
                                            <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="115" default="Exclusive"} {jrCore_lang skin=$_conf.jrCore_active_skin id="9" default="News"}</h2><br>
                                        </div>
                                        <div class="body_3 mb20">
                                            {if isset($item.blog_image_size) && $item.blog_image_size > 0}
                                            <div class="center mb10">
                                                <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{jrCore_module_function function="jrImage_display" module="jrBlog" type="blog_image" item_id=$item._item_id size="medium" alt=$item.blog_title crop="auto" class="iloutline"}</a>
                                            </div>
                                            {/if}
                                            <h3><a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}">{$item.blog_title}</a></h3><br>
                                            <div class="blog-text">
                                                {$item.blog_text|truncate:100:"...":false|jrCore_format_string:$item.profile_quota_id|nl2br}
                                            </div>
                                            {if jrCore_module_is_active('jrComment')}
                                            <div class="float-left">
                                                <a href="{$jamroom_url}/news_story/{$item._item_id}/{$item.blog_title_url}#comments"><span class="capital">{jrCore_lang module="jrBlog" id="27" default="comments"}</span>: {$item.blog_comment_count|default:0}</a>
                                            </div>
                                            {/if}
                                            <div class="float-right"><a onclick="jrLoad('#news_listing','{$jamroom_url}/news_list/{$item.blog_category}');$('html, body').animate({ scrollTop: $('#newslist').offset().top -100 }, 'slow');"><div class="button-more">&nbsp;</div></a></div>
                                            <div class="clear"></div>
                                        </div>
                                    {/foreach}
                                {/literal}
                            {/capture}

                            {jrCore_list module="jrBlog" order_by="_created desc" limit="1" search1="_user_id = 1" search2="blog_category = exclusive" template=$exclusive_news}

                            {* NEWEST VIDEO *}
                            <div class="body_2">
                                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="57" default="video"}</h2><br>
                            </div>
                            {capture name="row_template" assign="newest_video"}
                                {literal}
                                    {jrCore_module_url module="jrVideo" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_3 mb20">
                                            <div class="center mb10">
                                                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{jrCore_module_function function="jrImage_display" module="jrVideo" type="video_image" item_id=$item._item_id size="medium" alt=$item.blog_title crop="auto" class="iloutline"}</a>
                                            </div>
                                            Now Playing: <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.video_title|jrCore_url_string}">{$item.video_title}</a><br>
                                            By: <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a>
                                        </div>
                                    {/foreach}
                                {/literal}
                            {/capture}

                            {jrCore_list module="jrVideo" order_by="_created desc" limit="1" template=$newest_video}

                            {* EVENTS *}
                            <div class="body_2">
                                <h2>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="newest"} {jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="events"}</h2><br>
                            </div>
                            {capture name="row_template" assign="newest_events"}
                                {literal}
                                <div class="body_3 mb20">
                                    {jrCore_module_url module="jrEvent" assign="murl"}
                                    {foreach from=$_items item="item"}
                                        <div class="body_5 page" style="margin-right:0;">
                                            <h3><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.event_title_url}">{$item.event_title}</a></h3><br>
                                            {jrEvent_get_featuring json=$item.event_featuring assign="_f"}
                                            <span class="media_title">{jrCore_lang module="jrEvent" id="13" default="Featuring"}:</span> {foreach from=$_f item="featured"}<span class="normal">{$featured}&nbsp;&nbsp;</span>{/foreach}<br>
                                            <span class="media_title">{jrCore_lang module="jrEvent" id="11" default="Event Date"}:</span> <span class="normal">{$item.event_date|jrCore_date_format}</span><br>
                                            <span class="media_title">{jrCore_lang module="jrEvent" id="6" default="Event location"}:</span> <span class="normal">{$item.event_location|truncate:60}</span>
                                            {*** Only show ratings 120 minutes after event start ***}
                                            {math equation="x+7200" x=$item.event_date assign="end_time"}
                                            {if $end_time <= $smarty.now}
                                            <div style="padding:4px 0 8px 4px;">
                                                {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0 }
                                            </div>
                                            {/if}
                                        </div>
                                    {/foreach}
                                </div>
                                {/literal}
                            {/capture}

                            {jrCore_list module="jrEvent" order_by="_created desc" limit="5" template=$newest_events}

                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

{jrCore_include template="footer.tpl"}
