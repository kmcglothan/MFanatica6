<li data-id="{$_item_id}">
    {if strlen($doc_title_url) > 0}<a id="{$doc_title_url}"></a>{/if}
    <div id="c{$_item_id}">

        <div class="section_text">

            {if !empty($doc_title)}
                <div class="section_title">
                    <h2>{$doc_title}</h2>
                </div>
            {/if}

            {if jrProfile_is_profile_owner($_profile_id)}
                <script>$(function() { var mid = $('#m{$_item_id}'); $('#c{$_item_id}').hover(function() { mid.show(); }, function() { mid.hide(); } ); }); </script>
                {jrCore_module_url module="jrDocs" assign="murl"}
                <div id="m{$_item_id}" class="section_actions">
                    {jrCore_item_update_button module="jrDocs" action="`$murl`/section_update/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                    {jrCore_item_delete_button module="jrDocs" action="`$murl`/section_delete/id=`$_item_id`" profile_id=$_profile_id item_id=$_item_id}
                </div>
            {/if}

            <div class="center" style="width:100%">
                {if jrCore_module_is_active('jrYouTube') && $_conf.jrYouTube_load_on_click == 'on'}
                    {* Load the youtube player iframe on click *}
                    <div class="youtube-container" onmouseover="jrYouTube_show_hover_play(this,1)" onmouseout="jrYouTube_show_hover_play(this,0)">
                        <div class="youtube-player" id="yt{$doc_youtube}">
                            <div onclick="jrYouTube_urlscan_iframe('{$doc_youtube}')">
                                <img class="youtube-thumb" src="//i.ytimg.com/vi/{$doc_youtube}/hqdefault.jpg">
                                <div class="youtube-play-button"></div>
                            </div>
                        </div>
                    </div>
                {else}
                    <iframe id="ytplayer" type="text/html" width="690" height="450" src="//www.youtube.com/embed/{$doc_youtube}?rel=0" frameborder="0"></iframe>
                {/if}

                <div class="section_caption">

                    {$doc_content|jrCore_format_string:$profile_quota_id}

                </div>

            </div>
        </div>

    </div>

    {jrCore_include template="section_divider.tpl" module="jrDocs"}

</li>

