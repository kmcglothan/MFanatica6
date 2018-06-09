{jrCore_module_url module="jrAction" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">

            {jrCore_item_index_buttons module="jrAction" profile_id=$_profile_id}

        </div>
        <h1>{jrCore_lang module="jrAction" id="4" default="Timeline"}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$profile_url}">{$profile_name}</a>
            &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}">{jrCore_lang module="jrAction" id="4" default="Timeline"}</a>
            {if isset($_post.profile_actions) && $_post.profile_actions == 'mentions'}
            &raquo; <a href="{$jamroom_url}/{$profile_url}/{$murl}/mentions">{jrCore_lang module="jrAction" id="7" default="Mentions"}</a>
            {/if}
        </div>
    </div>

    {if jrProfile_is_profile_owner($_profile_id)}

        <div class="block_content">

            <div id="action_search" class="item left p10" style="display:none;">
                {jrCore_lang module="jrAction" id="8" default="Search" assign="svar"}
                {if $_post.profile_actions =='mentions'}
                    {* we're searching mentions *}
                    {$where = 'mentions'}
                {else}
                    {$where = 'search'}
                {/if}
                <form name="action_search_form" action="{$jamroom_url}/{$profile_url}/{$murl}/{$where}" method="get" style="margin-bottom:0">
                    <input type="text" name="ss" placeholder="{$svar}" class="form_text">
                    <input type="submit" class="form_button" value="{$svar}">
                </form>
            </div>

            {* we only show the new action form to the profile owner *}
            {if $quota_jrAction_can_post == 'on'}
                <div id="new_action" class="item">
                    {jrAction_form}
                </div>
            {/if}


            {* if we are viewing our own profile, include profile updates for profiles we follow *}

            {assign var="page_num" value="1"}
            {if isset($_post.p)}
                {assign var="page_num" value=$_post.p}
            {/if}

            {* See what we are loading - time line or mentions *}
            {if isset($_post.profile_actions) && $_post.profile_actions == 'mentions'}

                {if isset($_post.ss) && strlen($_post.ss) > 2}
                    {jrCore_list module="jrAction" search1="action_mention_`$_profile_id` = 1" search2="action_text like %`$_post.ss`%" order_by="_item_id desc" pagebreak=12 page=$page_num pager=true assign="timeline"}
                {else}
                    {jrCore_list module="jrAction" search="action_mention_`$_profile_id` = 1" order_by="_item_id desc" pagebreak=12 page=$page_num pager=true assign="timeline"}
                {/if}

            {elseif isset($_post.profile_actions) && $_post.profile_actions == 'search'}

                {jrCore_list module="jrAction" search="_item_id in `$_post.match_ids`" order_by="_item_id desc" pagebreak=12 page=$page_num pager=true assign="timeline"}

            {elseif isset($_post.profile_actions) && $_post.profile_actions == 'shared'}

                {jrCore_list module="jrAction" search="action_shared = `$_profile_id`" order_by="_item_id desc" pagebreak=12 page=$_post.p pager=true assign="timeline"}

            {else}

                {* If we are the site owner, we include action updates for profiles we follow *}
                {if jrUser_is_linked_to_profile($_profile_id)}
                    {jrCore_list module="jrAction" profile_id=$_profile_id order_by="_item_id desc" pagebreak=12 include_followed=true page=$page_num pager=true assign="timeline"}
                {else}
                    {jrCore_list module="jrAction" profile_id=$_profile_id order_by="_item_id desc" pagebreak=12 page=$page_num pager=true assign="timeline"}
                {/if}

            {/if}

            {if strlen($timeline) > 10}
            <div class="item" style="padding:0;border-bottom: none">
                <div id="timeline">
                    {$timeline}
                </div>
            </div>
            {/if}

        </div>

    {else}

        <div class="block_content">
            {jrCore_list module="jrAction" profile_id=$_profile_id order_by="_item_id desc" pagebreak=12 page=$_post.p pager=true assign="timeline"}
            {if strlen($timeline) > 10}
            <div class="item">
                <div id="timeline">
                    {$timeline}
                </div>
            </div>
            {/if}
        </div>

    {/if}

</div>

