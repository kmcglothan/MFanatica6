{jrCore_module_url module="jrGroup" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="container">
    <div class="row">
        <div class="col12 last">
            <div class="box">
                <div class="title">
                    {jrSearch_module_form search_url="`$_post.module_url`/`$murl`/`$_post._1`/`$_post._2`/`$_post._3`"}
                    <h1>{$item.group_title} Members</h1>
                    <div class="breadcrumbs">
                        <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrGroup" id="1" default="Groups"}</a> &raquo;
                        <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.group_title_url}">{$item.group_title}</a> &raquo; Members
                    </div>
                </div>
            </div>
            <div class="block_content">
                <div class="item">
                    {if $item.group_member}
                        {foreach $item.group_member as $member}
                            <div class="center" style="float:left">
                                {if $member.member_status == 0}{jrCore_lang module="jrGroup" id=71 default="pending" assign="status"}{elseif $member.member_status == 1}{jrCore_lang module="jrGroup" id=63 default="active" assign="status"}{else}{jrCore_lang module="jrGroup" id=72 default="pending deletion" assign="status"}{/if}
                                {if $member.member_status == 0} {* pending *}
                                    {if jrProfile_is_profile_owner($item._profile_id)}
                                        <a href="{$jamroom_url}/{$member.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$member._user_id size="medium" crop="auto" title="@{$member.profile_url} ({$status})" alt="@{$member.profile_url} ({$status})" class="img-{$member.member_status}"}</a><br><a href="{$jamroom_url}/{$member.profile_url}">@{$member.profile_url}</a>
                                    {/if}
                                {else}
                                    <a href="{$jamroom_url}/{$member.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$member._user_id size="medium" crop="auto" title="@{$member.profile_url} ({$status})" alt="@{$member.profile_url} ({$status})" class="img-{$member.member_status}"}</a><br><a href="{$jamroom_url}/{$member.profile_url}">@{$member.profile_url}</a>
                                {/if}
                                {if jrProfile_is_profile_owner($item._profile_id)}
                                    <br><small>[<a href="{$jamroom_url}/{$murl}/user_config/group_id={$item._item_id}/user_id={$member._user_id}">user config</a>]</small>
                                {/if}
                            </div>
                        {/foreach}
                        <div style="clear:both"></div>
                    {/if}
                </div>
            </div>
            {jrCore_include module="jrCore" template="list_pager.tpl"}
        </div>
    </div>
</div>
