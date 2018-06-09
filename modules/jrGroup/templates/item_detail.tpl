{jrCore_module_url module="jrGroup" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="container">
    <div class="row">
        <div class="col9">
            <div class="block">
                <div class="title">
                    <div class="block_config">
                        {if jrUser_is_admin() || !jrProfile_is_profile_owner($item._profile_id)}
                            {jrGroup_apply_button item=$item}
                        {/if}
                        {jrCore_item_detail_buttons module="jrGroup" item=$item}
                    </div>
                    <h1>{$item.group_title}</h1>
                    <div class="breadcrumbs">
                        <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrGroup" id=1 default="Groups"}</a> &raquo; {$item.group_title}
                    </div>
                </div>
                <div class="block_content">
                    <div class="item">
                        <div class="container">
                            <div class="row">
                                <div class="col3">
                                    <div class="block_image center p5">
                                        {if $item.group_private == 'on'}
                                            <div class="p5 center error">
                                                <span class="info">{jrCore_lang module="jrGroup" id="20" default="Private Group"}</span>
                                                {jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" alt=$item.group_title width=false height=false class="img_scale"}
                                            </div>
                                        {else}
                                            {jrCore_module_function function="jrImage_display" module="jrGroup" type="group_image" item_id=$item._item_id size="large" alt=$item.group_title width=false height=false class="img_scale"}
                                        {/if}
                                    </div>
                                </div>
                                <div class="col9 last">
                                    <div class="p5" style="padding-left:12px">
                                        {$item.group_description|jrCore_format_string:$item.profile_quota_id}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {if jrCore_module_is_active('jrGroupDiscuss')}
                        <div class="title">
                            <div class="block_config">
                                {if jrGroup_member_has_access($item)}
                                    {if $item.discuss_user_is_following == '1'}
                                        {jrCore_module_function function="jrGroupDiscuss_follow_group_button" icon="site-hilighted" group_id=$item._item_id size=16}
                                    {else}
                                        {jrCore_module_function function="jrGroupDiscuss_follow_group_button" icon="site" group_id=$item._item_id size=16}
                                    {/if}
                                {/if}
                                {jrCore_module_url module="jrGroupDiscuss" assign="ndurl"}
                                {if jrGroup_member_has_access($item) && jrCore_module_is_active('jrFeed')}
                                    {jrCore_module_url module="jrFeed" assign="furl"}
                                    <a href="{$jamroom_url}/{$furl}/{$ndurl}/limit=100/group_id={$item._item_id}">{jrCore_icon icon="rss" size=16}</a>
                                {/if}
                                {if jrProfile_is_profile_owner($item._profile_id) || jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $item, $_user._user_id) == 'on'}
                                    {jrCore_lang module="jrGroupDiscuss" id=2 default="Create New Discussion" assign="ttl"}
                                    <a href="{$jamroom_url}/{$ndurl}/create/group_id={$item._item_id}" title="{$ttl|jrCore_entity_string}">{jrCore_icon icon="plus" size=16}</a>
                                {/if}
                            </div>
                            <h3>{jrCore_lang module="jrGroupDiscuss" id=20 default="Discussion Forum"}</h3> &nbsp; <strong><small><a href="{$jamroom_url}/{$item.profile_url}/{$ndurl}/group_id={$item._item_id}">{jrCore_lang module="jrGroupDiscuss" id=21 default="View All"}</a></small></strong>
                        </div>
                        <div class="item">
                            {capture name="row_template" assign="dtpl"}
                            {literal}
                                {jrCore_module_url module="jrGroupDiscuss" assign="durl"}
                                {foreach $_items as $k => $item}
                                <div>
                                    <div class="p5">
                                        {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="small" width="48" height="48" crop="auto" title="{$item.user_name}" alt="{$item.user_name}" class="action_item_user_img"}
                                    </div>
                                    <div style="display:inline-block;padding-left:12px">
                                        <a href="{$jamroom_url}/{$_params._group.profile_url}/{$durl}/{$item._item_id}/{$item.discuss_title_url}"><h3>{$item.discuss_title}</h3></a>
                                        <br>
                                        <small>
                                            {if jrUser_is_logged_in() && jrGroup_member_has_access($item)}
                                            <span class="small_follow_container" style="vertical-align: middle">
                                                  {if $item.discuss_user_is_following == '1'}
                                                      {jrCore_module_function function="jrGroupDiscuss_follow_button" icon="site-hilighted" item_id=$item._item_id size="12"}
                                                  {else}
                                                      {jrCore_module_function function="jrGroupDiscuss_follow_button" icon="site" item_id=$item._item_id size="12"}
                                                  {/if}
                                            </span>
                                            {/if}
                                            <a href="{$jamroom_url}/{$item.home_profile_url}">@{$item.user_name}</a> {jrCore_lang module="jrGroupDiscuss" id=22 default="started"} {$item._created|jrCore_format_time:false:"relative"} - {jrCore_lang module="jrGroupDiscuss" id=23 default="replies"}: {$item.discuss_comment_count|default:0}
                                        </small>
                                    </div>
                                </div>
                                <div style="clear:both"></div>
                                {/foreach}
                            {/literal}
                            {/capture}
                            {jrCore_list module="jrGroupDiscuss" search="discuss_group_id = `$item._item_id`" order_by="_updated desc" limit=10 template=$dtpl _group=$item}
                        </div>
                    {/if}

                    {if jrGroup_member_has_access($item) && (!jrCore_module_is_active('jrComment') || !isset($item.group_wall) || $item.group_wall == 'off')}
                        {jrCore_item_detail_features module="jrGroup" item=$item exclude="jrComment~item_comments,jrDisqus~disqus_comment"}
                    {elseif jrGroup_member_has_access($item)}
                        <div class="title">
                            <h3>{jrCore_lang module="jrGroup" id=69 default="Comment Wall"}</h3>
                        </div>
                        {jrCore_item_detail_features module="jrGroup" item=$item exclude="jrAction~share_to_timeline"}
                    {elseif isset($_conf.jrGroup_comment_membership) && $_conf.jrGroup_comment_membership == 'on'}
                        <div class="title">
                            <h3>{jrCore_lang module="jrGroup" id=69 default="Comment Wall"}</h3>
                        </div>
                        {jrComment_form item_id=$item._item_id module="jrGroup" profile_id=$item._profile_id}
                    {/if}
                </div>
            </div>
        </div>
        <div class="col3 last">
            <div class="block" style="padding-left:0">
                <div class="title">
                    <h3>{jrCore_lang module="jrGroup" id=13 default="Members"}</h3> &nbsp; <strong><small><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/members/{$item._item_id}/{$item.group_title_url}">{jrCore_lang module="jrGroup" id=70 default="View All"}</a></small></strong>
                </div>
                <div class="item">
                    {if $item.group_member}
                        {foreach $item.group_member as $member}
                            {if $member@index < $_conf.jrGroup_max_images}
                                {if $member.member_status == 0}{jrCore_lang module="jrGroup" id=71 default="pending" assign="status"}{elseif $member.member_status == 1}{jrCore_lang module="jrGroup" id=63 default="active" assign="status"}{else}{jrCore_lang module="jrGroup" id=72 default="pending deletion" assign="status"}{/if}
                                {if jrProfile_is_profile_owner($item._profile_id)}
                                    <a href="{$jamroom_url}/{$murl}/user_config/group_id={$item._item_id}/user_id={$member.member_user_id}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$member.member_user_id size="xsmall" crop="auto" title="@{$member.profile_name} ({$status})" alt="@{$member.profile_name} ({$status})" class="img-{$member.member_status}" style="float:left"}</a>
                                {else}
                                    {if $member.member_status != 0}
                                        <a href="{$jamroom_url}/{$member.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$member.member_user_id size="xsmall" crop="auto" title="@{$member.profile_name}" alt="@{$member.profile_name}" class="img-1" style="float:left"}</a>
                                    {/if}
                                {/if}
                            {/if}
                        {/foreach}
                    {/if}
                    <div style="clear:both"></div>
                </div>
                {if jrCore_module_is_active('jrGroupPage')}
                    <div class="title">
                        {if jrProfile_is_profile_owner($item._profile_id) || jrGroup_get_user_config('jrGroupPage', 'allowed', $item, $_user._user_id) == 'on'}
                            <div class="block_config">
                                {jrCore_module_url module="jrGroupPage" assign="npurl"}
                                {jrCore_lang module="jrGroupPage" id=2 default="Create New Group Page" assign="pttl"}
                                <a href="{$jamroom_url}/{$npurl}/create/group_id={$item._item_id}" title="{$pttl|jrCore_entity_string}">{jrCore_icon icon="plus" size=16}</a>
                            </div>
                        {/if}
                        <h3>{jrCore_lang module="jrGroupPage" id=1 default="Group Pages"}</h3>
                    </div>
                    <div class="item">
                        {capture name="row_template" assign="tpl"}
                        {literal}
                            {jrCore_module_url module="jrGroupPage" assign="purl"}
                            {foreach $_items as $k => $item}
                            <a href="{$jamroom_url}/{$_params._group.profile_url}/{$purl}/{$item._item_id}/{$item.npage_title_url}">{$item.npage_title}</a><br>
                            {/foreach}
                        {/literal}
                        {/capture}
                        {jrCore_list module="jrGroupPage" search="npage_group_id = `$item._item_id`" order_by="_created desc" limit="100" template=$tpl _group=$item}
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>
