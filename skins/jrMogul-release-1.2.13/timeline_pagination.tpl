{if isset($_post.option) && is_numeric($_post.option)}
    {if $_conf.jrMogul_show_followed == 'on'}
        {jrCore_list module="jrAction" profile_id=$_post.option include_followed=true order_by="_item_id desc" pagebreak=12 page=$_post.p pager=true pager_template="timeline_pager.tpl"}
    {else}
        {jrCore_list module="jrAction" profile_id=$_post.option order_by="_item_id desc" pagebreak=12 page=$_post.p pager=true pager_template="timeline_pager.tpl"}
    {/if}
{else}
    {jrCore_list module="jrAction" search="action_mode not_in update,delete" order_by="_item_id desc" pagebreak="12" page=$_post.p pager=true pager_template="timeline_pager.tpl"}
{/if}
