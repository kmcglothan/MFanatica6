{jrCore_module_url module="jrForum" assign="murl"}

<div class="block">

    <div class="title">

        <div class="block_config">
            {jrCore_lang module="jrCore" id="8" default="search" assign="alt"}
            <a href="javascript:void(0)" title="{$alt}" onclick="$('#forum_search').slideToggle(300, function() { $('#forum_search_text').focus(); } );">{jrCore_icon icon="search2"}</a>
        </div>

        <h1>{jrCore_lang module="jrForum" id="36" default="Forum"}</h1><br>

        <div class="breadcrumbs">
            {if isset($_post.p) && is_numeric($_post.p) && $_post.p > 1}
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a> &raquo; {jrCore_lang module="jrForum" id="56" default="Page"} {$_post.p}
            {else}
                <a href="{$jamroom_url}/{$profile_url}/">{$profile_name}</a> &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrForum" id="36" default="Forum"}</a>
            {/if}
        </div>

    </div>

    <div id="forum_search" class="block_content" style="display:none">
        <div class="item">
            {if isset($search_string_value) && $found_forum_posts === 0}
                <div class="item error">{jrCore_lang module="jrForum" id="52" default="There were no topics found that matched your search term"}</div>
            {/if}
            <form id="forum_search_form" method="get" action="{$jamroom_url}/{$profile_url}/{$murl}">
                <input type="text" id="forum_search_text" name="search_string" value="{$search_string_value}" class="form_text form_search_text" tabindex="1" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) { jrForum_search_submit(); }"><br><br>
                {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
                {jrCore_image image="submit.gif" id="form_submit_indicator" width="24" height="24" alt=$working}<input type="button" id="forum_search_submit" class="form_button" value="search" tabindex="2" onclick="jrForum_search_submit();">
            </form>
        </div>
    </div>

    {if is_array($_items)}
    <div class="block_content">
        <div class="item">
        {foreach $_items as $cat}
            <div class="p5 mb5">
                <div>
                    <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$cat.cat_title_url}">
                    {if $cat.cat_new_topic_posts == '1' && $cat.cat_topic_count > 0}
                        <div class="forum_post_count forum_post_count_new{$pinned_class}">
                            {$cat.cat_topic_count|number_format} {jrCore_lang module="jrForum" id="60" default="topics"} {if $cat.cat_update_user._user_id > 0}<span style="display:inline-block;margin-left:6px" title="{jrCore_lang module="jrForum" id="34" default="update by"} {$cat.cat_update_user.user_name}, {$cat.cat_updated|jrCore_format_time:false:"relative"}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$cat.cat_update_user._user_id size="small" crop="auto" width="32" alt=$cat.cat_update_user.user_name _v=$cat.cat_updated}</span>{/if}
                        </div>
                    {else}
                        <div class="forum_post_count{$pinned_class}">
                            {$cat.cat_topic_count|number_format} {jrCore_lang module="jrForum" id="60" default="topics"} {if $cat.cat_update_user._user_id > 0}<span style="display:inline-block;margin-left:6px" title="{jrCore_lang module="jrForum" id="34" default="update by"} {$cat.cat_update_user.user_name}, {$cat.cat_updated|jrCore_format_time:false:"relative"}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$cat.cat_update_user._user_id size="small" crop="auto" width="32" alt=$cat.cat_update_user.user_name _v=$cat.cat_updated}</span>{/if}
                        </div>
                    {/if}
                    </a>

                    <h2>
                        <a href="{$jamroom_url}/{$profile_url}/{$murl}/{$cat.cat_title_url}">{$cat.cat_title}</a>
                    </h2>
                    <br><span class="normal">
                    {if strlen($cat.cat_desc) > 0}
                        &raquo; {$cat.cat_desc}
                    {else}
                        &nbsp;
                    {/if}
                    </span>
                </div>
            </div>
        {/foreach}
        </div>

    </div>
    {/if}

    <div class="item">
        {jrForum_active_users profile_id=$_profile_id}
        <div style="clear:both"></div>
    </div>

</div>