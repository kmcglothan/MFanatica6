{jrCore_module_url module="jrForum" assign="murl"}

<div class="block">

    {assign var="cat" value=""}
    {assign var="sch" value=""}
    {if strlen($category_url) > 0}
        {assign var="cat" value="&raquo; <a href=\"`$jamroom_url`/`$profile_url`/`$murl`/`$category_url`\">`$topic.forum_cat`</a>"}
        {assign var="sch" value="/`$category_url`"}
    {/if}

    <div class="title">
        <div class="block_config">
            {if jrUser_is_logged_in()}
                {jrForum_solution_button item=$topic}
                {if $forum_user_is_following == '1'}
                    {jrCore_module_function function="jrForum_follow_button" icon="site-hilighted" item_id=$topic._item_id}
                {else}
                    {jrCore_module_function function="jrForum_follow_button" icon="site" item_id=$topic._item_id}
                {/if}
            {/if}
        </div>
        <h1>
        {if isset($topic.forum_solution) && strlen($topic.forum_solution) > 1}
            <span class="section_solution_detail" style="background-color:{$topic.forum_solution_color}">{$topic.forum_solution}</span>
        {else}
            <span class="section_solution_detail" style="display:none"></span>
        {/if}
        {$topic.forum_title}</h1>
        <div class="forumCrumbs">
            {if isset($search_string_value)}
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat} &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}{$breadcrumb_url}">{jrCore_lang module="jrForum" id="53" default="Search Results"}</a> &raquo; {$topic.forum_title}
            {elseif strlen($breadcrumb_url) > 0}
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat} &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}{$breadcrumb_url}">{jrCore_lang module="jrForum" id="56" default="Page"} {$forum_page_num}</a> &raquo; {$topic.forum_title}
            {else}
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> {$cat} &raquo; {$topic.forum_title}
            {/if}
        </div>
    </div>

    <div class="block_content">

    {foreach $_items as $_itm}
    <a id="r{$_itm._item_id}"></a>
    {if $_itm@last}
        <a id="last"></a>
    {/if}
    <div id="p{$_itm._item_id}" class="item" style="position:relative">
        <div style="display:table">
            <div style="display:table-row">
                {if !jrCore_is_mobile_device()}
                <div class="forum_post_image">
                    {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$_itm._user_id size="icon96" alt=$_itm.user_name class="action_item_user_img iloutline"}<br>
                    <span class="normal"><a href="{$jamroom_url}/{$_itm.profile_url}">@{$_itm.profile_url}</a><br>{$_itm._created|jrCore_format_time}<br><a href="{$jamroom_url}/{$murl}/activity/{$_itm._profile_id}/{$_itm.profile_url}">{$_itm.user_jrForum_item_count|default:0|number_format} {jrCore_lang module="jrForum" id="35" default="posts"}</a></span>
                </div>
                {/if}
                <div class="forum_post_text">

                    {if jrCore_is_mobile_device()}
                        <span class="normal"><a href="{$jamroom_url}/{$_itm.profile_url}">@{$_itm.profile_url}</a> &bull; {$_itm._created|jrCore_format_time} &bull; <a href="{$jamroom_url}/{$murl}/activity/{$_itm._profile_id}/{$_itm.profile_url}">{$_itm.user_jrForum_item_count|default:0|number_format} {jrCore_lang module="jrForum" id="35" default="posts"}:</a></span><br><br>
                    {/if}

                    {if isset($_conf.jrForum_editor) && $_conf.jrForum_editor == 'on'}
                        {$_itm.forum_text|jrCore_format_string:$_itm.profile_quota_id}
                    {else}
                        {$_itm.forum_text|jrCore_format_string:$_itm.profile_quota_id:null:"html"}
                    {/if}


{* do not indent this or the quoting looks funny *}
{if jrUser_is_logged_in()}
<div id="q{$_itm._item_id}" style="display:none">[quote="{$_itm.user_name}"]
{if isset($_conf.jrForum_editor) && $_conf.jrForum_editor == 'on'}{$_itm.forum_text|trim}{else}{$_itm.forum_text|trim|htmlentities}{/if}
[/quote]
</div>
{/if}

                    {* See if this post has a file attachment *}
                    {if $_conf.jrForum_max_attachments > 0}
                    {jrCore_get_uploaded_files module="jrForum" item=$_itm field="forum_file" multiple=$_conf.jrForum_max_attachments}
                    {else}
                    {jrCore_get_uploaded_files module="jrForum" item=$_itm field="forum_file"}
                    {/if}

                    {jrForum_get_signature item=$_itm}

                    {* see if this post has been edited *}
                    {math equation="x - y" x=$_itm._updated y=$_itm._created assign="m_diff"}
                    {if $m_diff > 10}
                        <br><span class="normal"><small>{jrCore_lang module="jrForum" id="34" default="updated by"} <a href="{$jamroom_url}/{$_itm.profile_url}">@{$_itm.profile_url}</a>: {$_itm._updated|jrCore_format_time}</small></span>
                    {/if}

                </div>
            </div>
        </div>

        {if jrUser_is_logged_in()}
        <div id="m{$_itm._item_id}" class="forum_actions">

            <script type="text/javascript">$(function () { $('#p{$_itm._item_id}').hover(function() { $('#m{$_itm._item_id}').toggle(); }); });</script>

            {if (jrProfile_is_profile_owner($_profile_id) || $_user.quota_jrForum_can_post == 'on') && $_itm.forum_locked != 'on'}
                {if isset($_conf.jrForum_editor) && $_conf.jrForum_editor == 'on'}
                    <a onclick="jrForumEditorQuotePost({$_itm._item_id});" title="{jrCore_lang module="jrForum" id="47" default="quote this"}">{jrCore_icon icon="quote"}</a>
                {else}
                    <a onclick="jrForumQuotePost({$_itm._item_id});" title="{jrCore_lang module="jrForum" id="47" default="quote this"}">{jrCore_icon icon="quote"}</a>
                {/if}
            {/if}

            {if jrProfile_is_profile_owner($_profile_id) || ($_itm._user_id == $_user._user_id && $_itm.forum_locked != 'on')}
                {jrCore_item_update_button module="jrForum" profile_id=$_user._profile_id item_id=$_itm._item_id}
                {jrCore_item_delete_button module="jrForum" action="`$murl`/post_delete_save/id=`$_itm._item_id`" profile_id=$_user._profile_id item_id=$_itm._item_id}
            {/if}

        </div>
        {/if}

    </div>
    {/foreach}

    {if $_conf.jrForum_post_pagebreak > 0 && $_conf.jrForum_post_pagebreak < $topic.forum_post_count && $info.this_page <= $info.total_pages}
        {jrCore_include module="jrCore" template="list_pager.tpl"}
    {/if}

    {* See if this topic is auto locked *}
    {if jrUser_is_logged_in() && !empty($topic.forum_locked) && $topic.forum_locked == 'on'}
        <div class="forum_locked">
            {jrCore_lang module="jrForum" id="33" default="This topic is closed and is no longer open for responses"}
        </div>
    {/if}

    {* New entry form *}
    {if jrUser_is_admin() || (jrUser_is_logged_in() && $_user.quota_jrForum_can_post == 'on')}
        {if jrUser_is_admin() || $topic.forum_locked != 'on'}
            {if $_conf.jrForum_allow_private == 'off' && $_user.profile_private != '1'}

                <div class="item error">
                    {jrCore_module_url module="jrProfile" assign="purl"}
                    {jrCore_lang module="jrForum" id=112 default="In order to post to this forum your Profile Privacy must be Global."}<br>
                    <a href="{$jamroom_url}/{$purl}/settings/hl=profile_private">{jrCore_lang module="jrForum" id=113 default="Click here to change your Profile Settings"}</a>
                </div>

            {else}

                <br>
                <div class="item" style="display:table">
                    <div style="display:table-row">
                        <div class="p5" style="display:table-cell;width:5%;vertical-align:top;">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$_user._user_id size="icon96" alt=$item.user_name class="action_item_user_img iloutline"}
                        </div>
                        <div class="p5" style="display:table-cell;width:95%;padding:5px 12px;">

                            <div class="forum_new_post_form">
                                <div id="forum_notice" style="display:none;"><!-- any forum errors load here --></div>
                                <form id="cform" action="{$jamroom_url}/{$murl}/post_create_save" method="POST" onsubmit="jrForumPostResponse('#cform');return false">
                                <input type="hidden" id="forum_group_id" name="forum_group_id" value="{$topic._item_id}">
                                <input type="hidden" id="forum_profile_id" name="forum_profile_id" value="{$topic.forum_profile_id}">
                                {if isset($_conf.jrForum_editor) && $_conf.jrForum_editor == 'on'}
                                    {jrCore_editor_field name="forum_text"}
                                {else}
                                    <textarea id="forum_new_post_textarea" name="forum_text" cols="40" rows="6" class="form_textarea" style="width:98%;"></textarea><br>
                                {/if}
                                <div style="vertical-align:middle;">
                                    {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                                    {jrCore_image image="submit.gif" id="form_submit_indicator" width="24" height="24" alt=$working style="margin:8px 8px 0px 8px;"}<input id="forum_submit" type="submit" value="Post Response" class="form_button" style="margin-top:8px;">
                                    {if jrCore_module_is_active('jrAction') && $_conf.jrForum_timeline == "on"}
                                        <input type="checkbox" id="jraction_add_to_timeline" name="jraction_add_to_timeline" checked="checked"> <span class="normal capital">{jrCore_lang module="jrAction" id=13 default="add to timeline"}</span><br>
                                    {/if}


                                    {if $_conf.jrForum_editor != 'on'}
                                        {* bb code help*}
                                        {jrCore_lang module="jrForum" id=109 default="BBCode Help" assign="bbt"}
                                        <div id="bbcode_help_button"><input type="button" value="{$bbt|jrCore_entity_string}" class="form_button" style="" onclick="jrForum_show_bbcode_help();"></div>
                                    {/if}

                                    {if $_user.quota_jrForum_file_attachments == 'on'}
                                    <div class="jrforum_upload_attachment">
                                        {jrCore_upload_button module="jrForum" field="forum_file" allowed='png,jpg,gif,jpeg,txt,zip,pdf' multiple="true"}
                                    </div>
                                    {/if}

                                </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                <div id="bbcode_help" class="item" style="height:auto;display:none">
                    <div class="bbcode_help_section">
                        {* BBCode Help will load here *}
                    </div>
                </div>

            {/if}
        {/if}
    {/if}

    {jrCore_item_detail_features module="jrForum" item=$topic exclude="jrComment~item_comments,jrDisqus~disqus_comments"}

    </div>

</div>
