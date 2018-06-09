{* default index for profile *}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}
{jrCore_module_url module="jrAction" assign="murl"}

{if $_profile_id == jrUser_get_profile_home_key('_profile_id')}
<script type="text/javascript">
$(document).ready(function() {
    jrMogul_watch_timeline();
});
</script>
{/if}

<div class="page_nav clearfix">
    <div class="row" style="overflow: visible;">
        <div class="col8">
            <div class="breadcrumbs">
                {jrCore_include template="profile_header_minimal.tpl"}

                {if $_post._1 == 'mentions' || $_post._1 == 'feedback'}
                    {$apage = $_post._1}
                {else}
                    {$apage = 'index'}
                {/if}
                {jrMogul_breadcrumbs module="jrAction" profile_url=$profile_url profile_name=$profile_name page=$apage}

            </div>
        </div>
        <div class="col4">
            <div class="action_buttons">
                {jrCore_item_index_buttons module="jrAction" profile_id=$_profile_id}
            </div>
        </div>
    </div>
</div>

<div class="col3">
    {if jrUser_is_logged_in() && jrUser_is_linked_to_profile($_profile_id)}
        {jrCore_include template="timeline_menu.tpl"}
    {else}
        {jrCore_include template="profile_info.tpl"}
    {/if}
    {jrCore_include template="trending.tpl"}

</div>

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

                {if jrUser_is_logged_in() && jrUser_is_linked_to_profile($_profile_id)}
                    {jrCore_include template="action_input.tpl"}
                {/if}

                {* See what we are loading - time line or mentions *}
                {if isset($_post.profile_actions) && $_post.profile_actions == 'mentions'}

                    {if isset($_post.ss) && strlen($_post.ss) > 2}
                        {jrCore_list module="jrAction" search1="action_mention_`$_profile_id` = 1" search2="action_text like %`$_post.ss`%" order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}
                    {else}
                        {jrCore_list module="jrAction" search="action_mention_`$_profile_id` = 1" order_by="_item_id desc" simplepagebreak=12 page=$page_num pager=true pager_template="timeline_pager.tpl"}
                    {/if}
                    {jrMogul_clear_notifications key='jrAction_mentions'}

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

                    {jrMogul_clear_notifications key='jrComment_comments'}
                    {jrMogul_clear_notifications key='jrLike_likes'}
                    {jrMogul_clear_notifications key='jrLike_dislikes'}
                    {jrMogul_clear_notifications key='jrRating_ratings'}

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

{jrCore_include template="profile_right.tpl"}

