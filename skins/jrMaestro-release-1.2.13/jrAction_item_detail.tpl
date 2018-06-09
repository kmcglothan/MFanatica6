{jrCore_module_url module="jrAction" assign="murl"}
{jrProfile_disable_header}
{jrProfile_disable_sidebar}

<div class="page_nav clearfix">
    <div class="row" style="overflow:visible;">
        <div class="col8">
            <div class="breadcrumbs">
                {jrCore_include template="profile_header_minimal.tpl"}
                {jrMaestro_breadcrumbs module="jrAction" profile_url=$item.profile_url profile_name=$item.profile_name page="index"}
            </div>
        </div>
        <div class="col4">
            <div class="action_buttons">
                {jrCore_item_detail_buttons module="jrAction" profile_id=$item._profile_id item=$item}
            </div>
        </div>
    </div>
</div>

<div class="col3">

    {if jrUser_is_logged_in() && jrUser_is_linked_to_profile($item._profile_id)}
        {jrCore_include template="timeline_menu.tpl"}
    {else}
        {jrCore_include template="profile_info.tpl"}
    {/if}

    {jrCore_include template="trending.tpl"}
</div>

<div class="col6">

    <div style="padding: 2px 10px;" id="action_detail">

        {if isset($item.action_original_profile_url)}

            {jrCore_module_url module='jrAction' assign="murl"}

            <div class="action">
                <div class="wrap">
                    <div class="share_wrap">
                        <div class="shared">
                            <div class="share_image">
                                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" class="img_scale" alt=$item.user_name}
                            </div>
                            <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
                            shared <span class="action_user_name"><a href="#">{$item.action_original_profile_url}'s</a> </span> {jrCore_module_url module=$item.action_module}
                        </div>
                    </div>
                    {if isset($item.action_data) && strlen($item.action_data) > 0}
                        {$item.action_data}
                    {else}
                        <div class="action_info">
                            <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.action_original_profile_url}')">
                                {jrCore_module_function
                                function="jrImage_display"
                                module="jrUser"
                                type="user_image"
                                item_id=$item.action_original_user_id
                                size="icon"
                                crop="auto"
                                alt=$item.$item.action_original_user_name
                                }
                            </div>
                            <div class="action_data">
                                <div class="action_delete">
                                    {jrCore_item_update_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                                    {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                                </div>
                                <span class="action_user_name"><a href="{$jamroom_url}/{$item.action_original_profile_url}" title="{$item.action_original_profile_name|jrCore_entity_string}">@{$item.action_original_profile_url}</a></span>
                                <span class="action_desc"><a href="{$jamroom_url}/{$item.action_original_profile_url}/{$murl}/{$item.action_original_item_id}">Posted an update</a></span><br>
                                <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

                            </div>
                        </div>
                        <div class="wrap">
                            <div class="item_media">
                                <div class="wrap">{$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}</div>
                            </div>
                        </div>
                    {/if}
                    <div class="action_feedback">
                        {jrMaestro_feedback_buttons module="jrAction" item=$item}
                        {if jrCore_module_is_active('jrRating')}
                            <div class="rating" id="jrAction_{$item._item_id}_rating">{jrCore_module_function
                                function="jrRating_form"
                                type="star"
                                module="jrAction"
                                index="1"
                                item_id=$item._item_id
                                current=$item.action_rating_1_average_count|default:0
                                votes=$item.action_rating_1_number|default:0}</div>
                        {/if}
                        {jrComment_form
                        item_id=$item._item_id
                        module="jrAction"
                        profile_id=$item._profile_id}
                    </div>
                </div>
            </div>

        {* Activity Updates *}
        {elseif isset($item.action_text)}

            <div class="action">
                <div class="wrap">
                    <div class="action_info">
                        <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
                            {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$item._user_id size="icon" crop="auto" alt=$item.user_name}
                        </div>
                        <div class="action_data">
                            <div class="action_delete">
                                {jrCore_item_update_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                                {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
                            </div>
                            <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">@{$item.profile_url}</a></span>
                            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}">Posted an update</a></span><br>
                            <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>
                        </div>
                    </div>
                    <div class="action_media">
                        <div class="item_media action_text clearfix">
                            {$item.action_text|jrCore_format_string:$item.profile_quota_id|jrAction_convert_hash_tags}
                        </div>
                    </div>
                    <div class="action_feedback">
                        {jrMaestro_feedback_buttons module="jrAction" item=$item}
                        {if jrCore_module_is_active('jrRating')}
                            <div class="rating" id="jrAction_{$item._item_id}_rating">{jrCore_module_function
                                function="jrRating_form"
                                type="star"
                                module="jrAction"
                                index="1"
                                item_id=$item._item_id
                                current=$item.action_rating_1_average_count|default:0
                                votes=$item.action_rating_1_number|default:0}</div>
                        {/if}
                        {jrComment_form item_id=$item._item_id module="jrAction" profile_id=$item._profile_id}
                    </div>
                </div>
            </div>


        {* Registered Module Action templates *}
        {elseif isset($item.action_data) && strpos($item.action_data, '{') !== 0}

            <div class="action">

                {$item.action_data}

                <div class="action_feedback">
                    {jrMaestro_feedback_buttons module="jrAction" item=$item}
                    {if jrCore_module_is_active('jrRating')}
                        <div class="rating" id="jrAction_{$item._item_id}_rating">{jrCore_module_function
                            function="jrRating_form"
                            type="star"
                            module="jrAction"
                            index="1"
                            item_id=$item._item_id
                            current=$item.action_rating_1_average_count|default:0
                            votes=$item.action_rating_1_number|default:0}</div>
                    {/if}
                    {jrComment_form item_id=$item._item_id module="jrAction" profile_id=$item._profile_id}
                </div>
            </div>

        {/if}

    </div>



</div>

{jrCore_include template="profile_right.tpl"}



