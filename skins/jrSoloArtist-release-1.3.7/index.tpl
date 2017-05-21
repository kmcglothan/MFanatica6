{assign var="selected" value="home"}
{jrCore_include template="header.tpl"}

<div class="container">

    <div class="row">

        <div class="col3">

            <div class="block">
                <div class="title mb10">
                    <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="latest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="songs"}:</h1>
                </div>
                <div class="block_content">
                    {jrCore_list module="jrAudio" order_by="_created desc" template="index_list_songs.tpl" limit="1" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" require_image="audio_image"}
                    <div class="block_config normal capital">
                        <a href="{$jamroom_url}/music">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="more"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="13" default="songs"}&nbsp;&raquo;</a>
                    </div>
                </div>
            </div>

            <div class="block">
                <div class="title mb10">
                    <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="11" default="latest"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="50" default="video"}:</h1>
                </div>
                <div class="block_content">
                    {jrCore_list module="jrVideo" order_by="_created desc" template="index_list_videos.tpl" limit="1" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" require_image="video_image"}
                    <div class="block_config normal capital">
                        <a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/video">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="more"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="14" default="videos"}&nbsp;&raquo;</a>
                    </div>
                </div>
            </div>

            {* sitewide tag cloud*}
            {jrTags_cloud height="300" assign="tag_cloud"}
            {if strlen($tag_cloud) > 0}
                <div class="block">
                    <div class="title mb10">
                        <h1>Tag Cloud</h1>
                    </div>
                    <div class="block_content">
                        <div class="p5 center top">
                            {$tag_cloud}
                        </div>
                    </div>
                </div>
            {/if}

        </div>

        <div class="col3">

            <div class="block">
                <div class="title mb10">
                    <h1>{jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="tour dates"}:</h1>
                </div>
                <div class="block_content">
                    {jrCore_list module="jrEvent" order_by="event_date numerical_desc" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" template="index_list_events.tpl" limit="5" require_image="event_image"}
                    <div class="block_config normal capital">
                        <a href="{$jamroom_url}/{$_conf.jrSoloArtist_main_profile_url|replace:' ':'-'}/event">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="more"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="19" default="tour dates"}&nbsp;&raquo;</a>
                    </div>
                </div>
            </div>

        </div>

        <div class="col6 last">

            {if jrCore_module_is_active('jrBlog') && (isset($_conf.jrSoloArtist_index_content) && $_conf.jrSoloArtist_index_content == 'blog')}
                {jrCore_list module="jrBlog" order_by="_created numerical_desc" limit="4" search1="_user_id = `$_conf.jrSoloArtist_main_id`" template="blogs_row.tpl"}
                <div class="p10 divider" style="width:90%;margin:15px auto;"> </div>
                <div class="normal right capital" style="padding:10px 30px 0 0;"><a href="{$jamroom_url}/blogs">{jrCore_lang skin=$_conf.jrCore_active_skin id="48" default="more"}&nbsp;{jrCore_lang skin=$_conf.jrCore_active_skin id="42" default="blogs"}&nbsp;&raquo;</a></div>
            {elseif jrCore_module_is_active('jrComment') && (isset($_conf.jrSoloArtist_index_content) && $_conf.jrSoloArtist_index_content == 'comments')}
                {jrCore_list module="jrComment" order_by="_created NUMERICAL_DESC" limit="8" template="comments.tpl" assign="side_comments"}
                {if isset($side_comments) && strlen($side_comments) > 0}
                    {$side_comments}
                {elseif jrCore_module_is_active('jrFeed')}
                    {if jrCore_module_is_active('jrDisqus')}
                        {jrFeed_list name="all disqus site comments"}
                    {else}
                        {jrFeed_list name="jamroom facebook page"}
                    {/if}
                {/if}
            {else}
                {jrCore_list module="jrAction" order_by="_created desc" limit="8" search1="_profile_id = `$_conf.jrSoloArtist_main_id`" template="index_actions.tpl"}
            {/if}

        </div>

    </div>

</div>

{jrCore_include template="footer.tpl"}

