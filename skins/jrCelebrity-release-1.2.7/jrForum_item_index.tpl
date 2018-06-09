{jrCore_module_url module="jrForum" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="box" style="padding: 0;">
    <div class="box_body" style="border-radius: 8px;">
        <div class="wrap">
            <div class="page_content">
                <div class="wrap">
                    {assign var="cat" value=""}
                    {assign var="sch" value=""}
                    {assign var="hdr" value=""}
                    {if strlen($category_url) > 0}
                        {assign var="cat" value="&raquo; <a href=\"`$jamroom_url`/`$profile_url`/`$murl`/`$category_url`\">`$category_title`</a>"}
                        {assign var="sch" value="/`$category_url`"}
                        {assign var="hdr" value=" &raquo; `$category_title`"}
                    {/if}
                    {if $_post._1 == 'my_posts'}
                        {jrCore_lang module="jrForum" id=93 default="my posts" assign="myp"}
                        {assign var="cat" value=" &raquo; `$myp`"}
                        {assign var="hdr" value=" &raquo; `$myp`"}
                    {elseif $_post._1 == 'new_posts'}
                        {jrCore_lang module="jrForum" id=100 default="newest posts" assign="myp"}
                        {assign var="cat" value=" &raquo; `$myp`"}
                        {assign var="hdr" value=" &raquo; `$myp`"}
                    {/if}

                    <div>
                        <div class="action_buttons">
                            {if jrUser_is_logged_in()}
                                <div style="display: inline-block; margin: 0 0 5px 0; padding: 0;">
                                    {if isset($category_id) && $category_id > 0}
                                        {if $category_forum_user_is_following_category == '1'}
                                            {jrForum_follow_category_button icon="site-hilighted" cat_id=$category_id}
                                        {else}
                                            {jrForum_follow_category_button icon="site" cat_id=$category_id}
                                        {/if}
                                    {/if}
                                </div>
                            {/if}
                            {jrCore_lang module="jrCore" id="8" default="search" assign="alt"}
                            <a href="javascript:void(0)" title="{$alt}" onclick="$('#forum_search').slideToggle(300, function() { $('#forum_search_text').focus(); });">{jrCore_icon icon="search2"}</a>

                            {if jrUser_is_logged_in() && !isset($search_string_value)}
                                {jrCore_lang module="jrForum" id="49" default="mark all topics read" assign="alt"}
                                {jrCore_lang module="jrForum" id="50" default="mark all topics in this forum as read?" assign="con"}
                                <a href="{$jamroom_url}/{$murl}/mark_all_topics_read/{$_profile_id}{$sch}" title="{$alt}" onclick="if(!confirm('{$con}')) { return false }">{jrCore_icon icon="ok"}</a>
                            {/if}

                            {jrCore_lang module="jrForum" id="2" default="create new topic" assign="alt"}

                            {if $category_read_only == 'on'}
                                {if jrUser_is_admin() || jrProfile_is_profile_owner($_profile_id)}
                                    {if !isset($search_string_value) || strlen($search_string_value) === 0}
                                        {jrCore_item_create_button module="jrForum" profile_id=$_user._profile_id alt=$alt action="`$murl`/create`$sch`/profile_id={$profile_id}"}
                                    {/if}
                                {/if}
                            {elseif $_user.quota_jrForum_can_post == 'on' && $_post._1 != 'my_posts' && $_post._1 != 'new_posts'}
                                {if !isset($search_string_value) || strlen($search_string_value) === 0}
                                    <a href="{$jamroom_url}/{$murl}/create{$sch}/profile_id={$_profile_id}" title="{jrCore_lang module="jrCore" id="36" default="create"}">{jrCore_icon icon="plus"}</a>
                                {/if}
                            {/if}

                        </div>
                        <span class="title">{jrCore_lang module="jrForum" id="36" default="Forum"}{$hdr}</span><br>
                        <div>
                            {if isset($search_string_value) && $found_forum_posts > 0}
                                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat} &raquo; {jrCore_lang module="jrForum" id="53" default="Search Results"}
                            {elseif isset($_post.p) && is_numeric($_post.p) && $_post.p > 1}
                                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat} &raquo; {jrCore_lang module="jrForum" id="56" default="Page"} {$_post.p}
                            {else}
                                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat}
                            {/if}
                        </div>
                    </div>


                    {if isset($search_string_value) && $found_forum_posts === 0}
                    <div id="forum_search" class="block_content">
                        {else}
                        <div id="forum_search" class="block_content" style="display:none">
                            {/if}
                            <div class="item">
                                {if isset($search_string_value) && $found_forum_posts === 0}
                                    <div class="item error">{jrCore_lang module="jrForum" id="52" default="There were no topics found that matched your search term"}</div>
                                {/if}
                                <form id="forum_search_form" method="get" action="{$jamroom_url}/{$profile_url}/{$murl}{$sch}">
                                    <input type="text" id="forum_search_text" name="search_string" value="{$search_string_value}" class="form_text form_search_text" tabindex="{jrCore_next_tabindex}" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrForum_search_submit(); }"><br><br>
                                    {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                                    {jrCore_image image="submit.gif" id="form_submit_indicator" width="24" height="24" alt=$working}<input type="button" id="forum_search_submit" class="form_button" value="search" tabindex="2" onclick="jrForum_search_submit();">
                                </form>
                            </div>
                        </div>

                        {if isset($category_note) && strlen($category_note) > 0}
                            <div class="block_content">
                                <div id="cat_note" class="item">
                                    {$category_note|jrCore_format_string:$profile_quota_id}
                                </div>
                            </div>
                        {/if}

                        {if is_array($_items)}
                            {jrCore_lang module="jrForum" id="19" default="pinned" assign="pinned"}
                            <div class="block_content">
                                <div class="item">
                                    {foreach $_items as $item}
                                        <div style="padding:12px 0">
                                            <div style="float:left;padding-right:12px;">
                                                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="xsmall" crop="auto" alt=$item.user_name class="action_item_user_img iloutline" _v=$item._updated}
                                            </div>
                                            <div>
                                                {if $item.forum_pinned == 'on'}
                                                    {assign var="pinned_class" value=" forum_post_pinned"}
                                                {else}
                                                    {assign var="pinned_class" value=""}
                                                {/if}
                                                {if strlen($category_url) > 0}
                                                {if $_conf.jrForum_post_pagebreak > 0 && $item.forum_post_count > $_conf.jrForum_post_pagebreak}
                                                {math equation="x / y" x=$item.forum_post_count y=$_conf.jrForum_post_pagebreak assign="last_page"}
                                                <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$category_url}/{$item._item_id}/{$item.forum_title_url}/p={$last_page|ceil}#last">
                                                    {else}
                                                    <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$category_url}/{$item._item_id}/{$item.forum_title_url}#last">
                                                        {/if}
                                                        {else}
                                                        {if $_conf.jrForum_post_pagebreak > 0 && $item.forum_post_count > $_conf.jrForum_post_pagebreak}
                                                        {math equation="x / y" x=$item.forum_post_count y=$_conf.jrForum_post_pagebreak assign="last_page"}
                                                        <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$item._item_id}/{$item.forum_title_url}/p={$last_page|ceil}#last">
                                                            {else}
                                                            <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$item._item_id}/{$item.forum_title_url}#last">
                                                                {/if}
                                                                {/if}
                                                                {if $item.forum_new_topic_posts == '1'}
                                                                <div class="forum_post_count forum_post_count_new{$pinned_class}">
                                                                    {assign var="aclass" value="topic_unread"}
                                                                    {else}
                                                                    <div class="forum_post_count{$pinned_class}">
                                                                        {assign var="aclass" value="topic_read"}
                                                                        {/if}
                                                                        {$item.forum_post_count} {jrCore_lang module="jrForum" id="35" default="posts"} {if $item.forum_updated_user_id > 0}<span style="display:inline-block;margin-left:6px" title="{jrCore_lang module="jrForum" id="34" default="update by"} {$item.forum_updated_user_name}, {$item.forum_updated|jrCore_format_time:false:"relative"}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item.forum_updated_user_id size="small" crop="auto" width="24" alt=$item.forum_updated_user_name _v=$item._updated}</span>{/if}
                                                                    </div></a>

                                                            {if isset($search_string_value)}

                                                                <h2>
                                                                    {if $categories_enabled == 'on' && strlen($category_url) > 0}
                                                                        <a class="{$aclass}" href="{$jamroom_url}/{$profile_url}/{$murl}/{$category_url}/{$item._item_id}/{$item.forum_title_url}/search_string={$search_string_value|default:'#'|urlencode}">{$item.forum_title|truncate:60}</a>
                                                                    {else}
                                                                        <a class="{$aclass}" href="{$jamroom_url}/{$profile_url}/{$murl}/{$item._item_id}/{$item.forum_title_url}/search_string={$search_string_value|default:'#'|urlencode}">{$item.forum_title|truncate:60}</a>
                                                                    {/if}
                                                                </h2>

                                                            {else}

                                                                <h2>
                                                                    {if $categories_enabled == 'on' && strlen($category_url) > 0}
                                                                        <a class="{$aclass}" href="{$jamroom_url}/{$profile_url}/{$murl}/{$category_url}/{$item._item_id}/{$item.forum_title_url}">{$item.forum_title|default:'#'|truncate:60}</a>
                                                                    {else}
                                                                        <a class="{$aclass}" href="{$jamroom_url}/{$profile_url}/{$murl}/{$item._item_id}/{$item.forum_title_url}">{$item.forum_title|default:'#'|truncate:60}</a>
                                                                    {/if}
                                                                </h2>

                                                            {/if}

                                                            <br><span class="normal"><small>

                                                                    {if jrUser_is_logged_in()}
                                                                        <span class="small_follow_container">
                            {if $item.forum_user_is_following == '1'}
                                {jrForum_follow_button icon="site-hilighted" item_id=$item._item_id size="12"}
                            {else}
                                {jrForum_follow_button icon="site" item_id=$item._item_id size="12"}
                            {/if}
                        </span>
                                                                    {/if}

                                                                    {if isset($item.forum_solution) && strlen($item.forum_solution) > 1}
                                                                        <span class="section_solution_list" style="background-color:{$item.forum_solution_color}">{$item.forum_solution}</span>
                                                                    {/if}

                                                                    {if $categories_enabled == 'on' && ($_post._1 == 'my_posts' || $_post._1 == 'new_posts')}
                                                                        <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$item.forum_cat_url}">{$item.forum_cat}</a> &bull;
                                                                    {/if}
                                                                    {if $item._created == $item.forum_updated}
                                                                        <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a>, {$item.forum_updated|jrCore_format_time:false:"relative"}
                                                                    {else}
                                                                        <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a>, {jrCore_lang module="jrForum" id="34" default="updated by"} <a href="{$jamroom_url}/{$item.forum_updated_profile_url}">@{$item.forum_updated_user_name}</a> {$item.forum_updated|jrCore_format_time:false:"relative"}
                                                                    {/if}
                                                                    {if isset($search_string_value)}
                                                                        &bull; <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$item.forum_cat_url}">{$item.forum_cat}</a>
                                                                    {/if}
                                                                </small></span>
                                            </div>
                                        </div>
                                    {/foreach}
                                </div>

                                {* prev/next page profile footer links *}
                                {if $info.prev_page > 0 || $info.next_page > 0}
                                    {jrCore_include module="jrCore" template="list_pager.tpl"}
                                {/if}

                            </div>

                            <div class="item">
                                {jrForum_active_users profile_id=$_profile_id}
                                <div style="clear:both"></div>
                            </div>

                        {else}

                            <div class="item">{jrCore_lang module="jrForum" id="96" default="There are no forum topics to show"}</div>

                        {/if}

                    </div>
                </div>
            </div>

        </div>
</div>

