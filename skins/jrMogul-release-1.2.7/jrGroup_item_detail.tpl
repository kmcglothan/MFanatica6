{jrCore_module_url module="jrGroup" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="breadcrumbs">
        {jrCore_include template="profile_header_minimal.tpl"}
        {jrMogul_breadcrumbs module="jrGroup" profile_url=$item.profile_url profile_name=$item.profile_name page="detail" item=$item}
    </div>
    <div class="action_buttons">
        {jrCore_item_detail_buttons module="jrGroup" item=$item}
        {if jrUser_is_admin() || !jrProfile_is_profile_owner($item._profile_id)}
            {jrGroup_apply_button item=$item}
        {/if}
    </div>
</div>

<div class="row">
    <div class="col9">
        <div class="box">
            {jrMogul_sort template="icons.tpl" nav_mode="jrGroup" profile_url=$profile_url}
            <div class="box_body">
                <div class="wrap detail_section">
                    <div id="list">
                        <div class="item clearfix">

                            <div class="block_image center">
                                {if $item.group_private == 'on'}
                                    <div class="media_image">
                                        {jrCore_module_function
                                        function="jrImage_display"
                                        module="jrGroup"
                                        type="group_image"
                                        item_id=$item._item_id
                                        size="xlarge"
                                        alt=$item.group_title
                                        width=false
                                        height=false
                                        crop="3:2"
                                        class="img_scale"
                                        }
                                        <span class="info">{jrCore_lang module="jrGroup" id="20" default="Private Group"}</span>
                                    </div>
                                {else}
                                    <div class="media_image">
                                        {jrCore_module_function
                                        function="jrImage_display"
                                        module="jrGroup"
                                        type="group_image"
                                        item_id=$item._item_id
                                        size="xlarge"
                                        alt=$item.group_title
                                        width=false
                                        height=false
                                        crop="3:2"
                                        class="img_scale"
                                        }
                                    </div>
                                {/if}
                            </div>
                            <span class="title">{$item.group_title}</span>
                            <div class="media_text">
                                {$item.group_description|jrCore_format_string:$item.profile_quota_id}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box">
            <ul class="head_tab">
                <li>
                    <a href="#" title="{jrCore_lang module="jrGroup" id=1 default="Group"}">{jrCore_icon icon="forum" size="20" color="444444"}</a>
                </li>
                <div class="action_buttons dark">
                    {if jrGroup_member_has_access($item)}
                        {if $item.discuss_user_is_following == '1'}
                            {jrCore_module_function function="jrGroupDiscuss_follow_group_button" icon="site-hilighted" color="444444" group_id=$item._item_id size=20}
                        {else}
                            {jrCore_module_function function="jrGroupDiscuss_follow_group_button" icon="site" color="444444" group_id=$item._item_id size=20}
                        {/if}
                    {/if}
                    {jrCore_module_url module="jrGroupDiscuss" assign="ndurl"}
                    {if jrCore_module_is_active('jrFeed')}
                        {jrCore_module_url module="jrFeed" assign="furl"}
                        <a href="{$jamroom_url}/{$furl}/{$ndurl}/limit=100/group_id={$item._item_id}">{jrCore_icon icon="rss" size=20 color="444444"}</a>
                    {/if}
                    {if jrProfile_is_profile_owner($item._profile_id) || jrGroup_get_user_config('jrGroupDiscuss', 'allowed', $item, $_user._user_id) == 'on'}
                        {jrCore_lang module="jrGroupDiscuss" id=2 default="Create New Discussion" assign="ttl"}
                        <a href="{$jamroom_url}/{$ndurl}/create/group_id={$item._item_id}"
                           title="{$ttl|jrCore_entity_string}">{jrCore_icon icon="plus" size=20 color="444444"}</a>
                    {/if}
                </div>
            </ul>
            <div class="box_body">
                <div class="wrap detail_section">
                    {if jrCore_module_is_active('jrGroupDiscuss')}
                        <div id="list">
                            {capture name="row_template" assign="dtpl"}
                            {literal}
                                {jrCore_module_url module="jrGroupDiscuss" assign="durl"}
                                {foreach $_items as $k => $item}
                                <div class="item clearfix">
                                    <div style="float: left;">
                                        {jrCore_module_function function="jrImage_display" module="jrUser"
                                        type="user_image" item_id=$item._user_id size="small" width="48" height="48"
                                        crop="auto" title="{$item.user_name}" alt="{$item.user_name}"
                                        class="action_item_user_img"}
                                    </div>
                                    <div style="display:inline-block;padding-left:12px">
                                        <a href="{$jamroom_url}/{$_params._group.profile_url}/{$durl}/{$item._item_id}/{$item.discuss_title_url}">
                                            <h3 style="margin: 0 0 5px;">{$item.discuss_title}</h3></a>
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
                                            <a href="{$jamroom_url}/{$item.home_profile_url}">@{$item.user_name}</a>
                                            {jrCore_lang module="jrGroupDiscuss" id=22 default="started"}
                                            {$item._created|jrCore_format_time:false:"relative"} - {jrCore_lang
                                            module="jrGroupDiscuss" id=23 default="replies"}:
                                            {$item.discuss_comment_count|default:0}
                                        </small>
                                    </div>
                                </div>
                                {/foreach}
                            {/literal}
                            {/capture}
                            {jrCore_list module="jrGroupDiscuss" search="discuss_group_id = `$item._item_id`" order_by="_updated numerical_desc" limit="10" template=$dtpl _group=$item}
                        </div>
                    {/if}

                    <div class="action_feedback" style="padding: 0">
                        {if jrGroup_member_has_access($item)}
                            {if !jrCore_module_is_active('jrComment') || !isset($item.group_wall) || $item.group_wall == 'off'}
                                {jrMogul_feedback_buttons module="jrGroup" comment=false item=$item}
                                {if jrCore_module_is_active('jrRating')}
                                    <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                                        function="jrRating_form"
                                        type="star"
                                        module="jrAudio"
                                        index="1"
                                        item_id=$item._item_id
                                        current=$item.audio_rating_1_average_count|default:0
                                        votes=$item.audio_rating_1_number|default:0}</div>
                                {/if}
                                {jrCore_item_detail_features module="jrGroup" item=$item exclude="jrComment~item_comments,jrDisqus~disqus_comment"}
                            {else}
                                {jrMogul_feedback_buttons module="jrGroup" item=$item}
                                {if jrCore_module_is_active('jrRating')}
                                    <div class="rating" id="jrAudio_{$item._item_id}_rating">{jrCore_module_function
                                        function="jrRating_form"
                                        type="star"
                                        module="jrAudio"
                                        index="1"
                                        item_id=$item._item_id
                                        current=$item.audio_rating_1_average_count|default:0
                                        votes=$item.audio_rating_1_number|default:0}</div>
                                {/if}
                                {jrCore_item_detail_features module="jrGroup" item=$item}
                            {/if}
                        {/if}
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="col3 last">
        <div class="box" style="padding-left:1em">
            <ul class="head_tab">
                <li>
                    <a href="#" title="{jrCore_lang module="jrGroup" id=149 default="Pages"}">{jrCore_icon icon="profile" size="20" color="444444"}</a>
                </li>
            </ul>
            <div class="box_body">
                <div class="wrap">
                    <div class="media">
                        <div class="clearfix" style="padding: 2px;">
                            {foreach $item.group_member as $member}
                                {if $member@index < $_conf.jrGroup_max_images}
                                    {if $member.member_status == 0}
                                        {jrCore_lang module="jrGroup" id=71 default="pending" assign="status"}
                                    {elseif $member.member_status == 1}
                                        {jrCore_lang module="jrGroup" id=63 default="active" assign="status"}
                                    {else}
                                        {jrCore_lang module="jrGroup" id=72 default="pending deletion" assign="status"}{/if}
                                    <div style="width: 33.3%; float: left;">
                                        <div style="padding: 2px;">
                                            {if jrProfile_is_profile_owner($item._profile_id)}
                                                <a href="{$jamroom_url}/{$murl}/user_config/group_id={$item._item_id}/user_id={$member.member_user_id}">
                                                    {jrCore_module_function
                                                    function="jrImage_display"
                                                    module="jrUser"
                                                    type="user_image"
                                                    item_id=$member.member_user_id
                                                    size="large"
                                                    crop="auto"
                                                    title="@{$member.profile_name} ({$status})"
                                                    alt="@{$member.profile_name} ({$status})"
                                                    class="img_scale"
                                                    }</a>
                                            {else}
                                                {if $member.member_status != 0}
                                                    <a href="{$jamroom_url}/{$member.profile_url}">
                                                        {jrCore_module_function
                                                        function="jrImage_display"
                                                        module="jrUser"
                                                        type="user_image"
                                                        item_id=$member.member_user_id
                                                        size="xsmall" crop="auto"
                                                        title="@{$member.profile_name}"
                                                        alt="@{$member.profile_name}"
                                                        class="img-1"
                                                        }</a>
                                                {/if}
                                            {/if}
                                        </div>
                                    </div>
                                {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {if jrCore_module_is_active('jrGroupPage')}
            <div class="box" style="padding-left:1em">
                <ul class="head_tab">
                    <li id="page_tab">
                        <a href="#" title="{jrCore_lang module="jrGroup" id=13 default="Members"}">{jrCore_icon icon="page" size="20" color="444444"}</a>
                    </li>
                    {if jrProfile_is_profile_owner($item._profile_id) || jrGroup_get_user_config('jrGroupPage', 'allowed', $item, $_user._user_id) == 'on'}
                        <div class="action_buttons">
                            {jrCore_module_url module="jrGroupPage" assign="npurl"}
                            {jrCore_lang module="jrGroupPage" id=2 default="Create New Group Page" assign="pttl"}
                            <a href="{$jamroom_url}/{$npurl}/create/group_id={$item._item_id}"
                               title="{$pttl|jrCore_entity_string}">{jrCore_icon icon="plus" size=20  color="444444"}</a>
                        </div>
                    {/if}
                </ul>
                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            <div class="wrap">
                                {capture name="row_template" assign="tpl"}
                                {literal}
                                    {jrCore_module_url module="jrGroupPage" assign="purl"}
                                    {foreach $_items as $k => $item}
                                    <a href="{$jamroom_url}/{$_params._group.profile_url}/{$purl}/{$item._item_id}/{$item.npage_title_url}">{$item.npage_title}</a>
                                    <br>
                                    {/foreach}
                                {/literal}
                                {/capture}
                                {jrCore_list module="jrGroupPage" search="npage_group_id = `$item._item_id`" order_by="_created desc" limit="100" template=$tpl _group=$item}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        {/if}
    </div>
</div>
