{jrCore_module_url module="jrAction" assign="murl"}
<div class="block">

    <div class="title">
        <h1>{jrCore_lang module="jrAction" id="11" default="Activity Stream"}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}">{$item.profile_name}</a>
            &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrAction" id="4" default="Timeline"}</a>
            &raquo; <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">{jrCore_lang module="jrAction" id="27" default="Activity Update"}</a>
        </div>
    </div>

    <div class="block_content">
        <div class="container">
            <div class="row">
                <div class="item">

                {* Mention *}
                {if isset($item.action_mode) && $item.action_mode == 'mention'}

                    {$item.action_text|jrCore_format_string:$item.profile_quota_id|jrCore_strip_html|truncate:160}

                {* Shared Action *}
                {elseif isset($item.action_shared)}

                    {if strlen($item.action_text) > 0}
                    <div class="action_item_text">
                        {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                    </div>
                    {/if}

                    {if strlen($item.action_original_html) > 0}
                    <div class="action_item_shared">
                        {$item.action_original_html|jrCore_format_string_clickable_urls}
                    </div>
                    {/if}

                {* Activity Update *}
                {elseif $item.action_module == 'jrAction' && isset($item.action_text)}

                    <div class="action_item_text">
                        {$item.action_text|jrCore_format_string:$item.profile_quota_id}
                    </div>

                {* Module Actions *}
                {elseif isset($item.action_html)}

                    {$item.action_html}

                {else}

                    <div class="action_item_text">
                        (no action data found)
                    </div>

                {/if}

                </div>
            </div>

            {if isset($item.action_shared_by_user_info) && count($item.action_shared_by_user_info) > 0}
            <div class="row">
                <div class="col12 last">
                    <div class="item">
                        {jrCore_lang module="jrAction" id=24 default="Shared By"}:<br>
                        <div class="p5">
                        {foreach $item.action_shared_by_user_info as $usr}
                            <div style="float:left"><a href="{$jamroom_url}/{$usr.profile_url}" title="@{$usr.profile_url}">{jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$usr._user_id size="xsmall" crop="auto" alt=$usr.user_name class="action_item_user_img img_shadow"}</a></div>
                        {/foreach}
                        </div>
                    </div>
                </div>
            </div>
            {/if}

        </div>
    </div>

    {if $item._profile_id == jrUser_get_profile_home_key('_profile_id')}
        {jrCore_item_detail_features module="jrAction" item=$item exclude="jrAction~share_to_timeline"}
    {else}
        {jrCore_item_detail_features module="jrAction" item=$item}
    {/if}

</div>

