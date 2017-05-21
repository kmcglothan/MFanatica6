{assign var="selected" value="members"}
{assign var="no_inner_div" value="true"}
{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" assign="page_title"}
{jrCore_page_title title=$page_title}
{jrCore_include template="header.tpl"}

{if isset($_post.list) && strlen($_post.list) > 0}

    {assign var="newclass" value="p_choice"}
    {if $_post.list == 'by_name'}
        {assign var="alphaclass" value="p_choice_active"}
        {assign var="order_by" value="profile_name asc"}
        {assign var="topclass" value="p_choice"}
    {elseif $_post.list == 'top'}
        {assign var="alphaclass" value="p_choice"}
        {assign var="topclass" value="p_choice_active"}
        {assign var="order_by" value="profile_view_count NUMERICAL_DESC"}
    {/if}

    {else}

    {assign var="newclass" value="p_choice_active"}
    {assign var="order_by" value="_created desc"}
    {assign var="alphaclass" value="p_choice"}
    {assign var="topclass" value="p_choice"}

{/if}

<div class="menu_tab">
    <div class="{$newclass}" onclick="jrCore_window_location('{$jamroom_url}/members');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="11" default="newest"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="members"}</div>
    <div class="{$alphaclass}" onclick="jrCore_window_location('{$jamroom_url}/members/list=by_name');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="members"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="48" default="by name"}</div>
    <div class="{$topclass}" onclick="jrCore_window_location('{$jamroom_url}/members/list=top');">{jrCore_lang  skin=$_conf.jrCore_active_skin id="31" default="top"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="58" default="members"}</div>
    <div class="clear"></div>
</div>
<div class="inner">
{if isset($_conf.jrNovaLight_require_images) && $_conf.jrNovaLight_require_images == 'on'}

    {if isset($_conf.jrNovaLight_member_quota) && $_conf.jrNovaLight_member_quota > 0}
        {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrNovaLight_member_quota`" tpl_dir="jrNovaLight" template="members_row.tpl" require_image="profile_image" pagebreak=$_conf.jrNovaLight_default_artist_pagebreak page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" tpl_dir="jrNovaLight" template="members_row.tpl" require_image="profile_image" pagebreak=$_conf.jrNovaLight_default_artist_pagebreak page=$_post.p}
    {/if}

{else}

    {if isset($_conf.jrNovaLight_artist_quota) && $_conf.jrNovaLight_artist_quota > 0}
        {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" search2="profile_quota_id in `$_conf.jrNovaLight_member_quota`" template="members_row.tpl" pagebreak=$_conf.jrNovaLight_default_artist_pagebreak page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" template="members_row.tpl" pagebreak=$_conf.jrNovaLight_default_artist_pagebreak page=$_post.p}
    {/if}

{/if}
</div>
{jrCore_include template="footer.tpl"}
