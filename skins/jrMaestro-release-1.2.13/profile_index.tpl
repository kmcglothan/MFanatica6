{* default index for profile *}
{jrCore_include template="profile_sidebar.tpl"}
<div class="col6">
    <div class="timeline_wrap">
        <div id="timeline">

            {assign var="page_num" value=1}
            {if isset($_post.p) && $_post.p > 1}
                {assign var="page_num" value=$_post.p}
            {/if}

            {if jrUser_is_linked_to_profile($_profile_id)}

                <div id="action_search" class="item left p10" style="display:none">
                    {jrCore_lang module="jrAction" id=8 default="Search" assign="svar"}
                    {if $_post.profile_actions =='mentions'}
                        {$where = 'mentions'}
                    {else}
                        {$where = 'search'}
                    {/if}
                    <form name="action_search_form" action="{$jamroom_url}/{$profile_url}/{$murl}/{$where}" method="get" style="margin-bottom:0">
                        <input type="text" name="ss" value="{$svar}" class="form_text" onfocus="if(this.value=='{$svar}'){ldelim} this.value=''; {rdelim}" onblur="if(this.value==''){ldelim} this.value='{$svar}'; {rdelim}">&nbsp;
                        <input type="submit" class="form_button" value="{$svar}">
                    </form>
                </div>

                {jrCore_include template="action_input.tpl"}

                {* See what we are loading - time line or mentions *}
            {if isset($_post.profile_actions) && $_post.profile_actions == 'mentions'}

                {if isset($_post.ss) && strlen($_post.ss) > 2}
                    {jrCore_list module="jrAction" search1="action_mention_`$_profile_id` = 1" search2="action_text like %`$_post.ss`%" order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}
                {else}
                    {jrCore_list module="jrAction" search="action_mention_`$_profile_id` = 1" order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}
                {/if}
                {jrMaestro_clear_notifications key='jrAction_mentions'}

            {elseif isset($_post.profile_actions) && $_post.profile_actions == 'search'}

                {jrCore_list module="jrAction" search="_item_id in `$_post.match_ids`" order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}

            {elseif isset($_post.profile_actions) && $_post.profile_actions == 'shared'}

                {jrCore_list module="jrAction" search="action_shared = `$_profile_id`" order_by="_item_id desc" simplepagebreak=12 page=$_post.p pager=true pager_template="timeline_pager.tpl"}

            {elseif isset($_post.profile_actions) && $_post.profile_actions == 'feedback'}

                {jrCore_list module="jrAction" search="action_feedback = `$_profile_id`" order_by="_item_id desc" simplepagebreak=12 page=$_post.p pager=true pager_template="timeline_pager.tpl"}

                {* clear the feedback counts *}
                <script>
                    $(document).ready(function() {ldelim}
                        jrProfile_reset_pulse_key('');
                        {rdelim});
                </script>

                {jrMaestro_clear_notifications key='jrComment_comments'}
                {jrMaestro_clear_notifications key='jrLike_likes'}
                {jrMaestro_clear_notifications key='jrLike_dislikes'}
                {jrMaestro_clear_notifications key='jrRating_ratings'}

            {else}

                {jrCore_list module="jrAction" order_by="_item_id desc" simplepagebreak=12 include_followed=true page=$page_num pager=true pager_template="timeline_pager.tpl"}

            {/if}


            {else}

                {jrCore_list module="jrAction" search="action_mode not_in update,delete" profile_id=$_profile_id order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}

            {/if}

            <div id="timeline_pagination_url" style="display:none">{$jamroom_url}/timeline_pagination/{$_profile_id}</div>

        </div>
    </div>
</div>


<div class="col2">
    <div class="box desk">
        <ul class="timeline_filter">
            {jrCore_list
            module="jrAction"
            profile_id=$_profile_id
            order_by="action_module asc"
            group_by="action_module"
            template="timeline_filter.tpl"
            }
        </ul>
    </div>
</div>
