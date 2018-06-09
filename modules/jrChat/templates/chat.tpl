<style type="text/css">
    {if $chat_state == 'closed' && !isset($mobile_view)}
    body {
        padding-right: 0
    }

    #jrchat-room {
        width: {$chat_width|default:'300px'};
        right: -{$chat_width|default:'300px'}
    }

    #jrchat-tabs {
        right: 0
    }

    {else}
    body {
        padding-right: {$chat_width|default:'300px'}
    }

    #jrchat-room {
        width: {$chat_width|default:'300px'}
    }

    #jrchat-tabs {
        right: {$chat_width|default:'300px'}
    }

    {/if}
</style>

{jrCore_module_url module="jrImage" assign="iurl"}
{jrCore_module_url module="jrUser" assign="uurl"}
{if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}
    <div id="jrchat-tabs">

        <div id="jrchat-hidden-tabs">

            <a id="jrchat-popout-tab" onclick="jrChat_popout()" title="{jrCore_lang module="jrChat" id=49 default="open chat in separate window"}" data-state="enabled">
                <div class="jrchat-tab">
                    <div class="jrchat-tab-inset">{jrCore_icon icon="chat-popout"}</div>
                </div>
            </a>

            <a id="jrchat-contract-tab" onclick="jrChat_contract()" title="{jrCore_lang module="jrChat" id=1 default="make chat pane narrower"}" data-state="enabled">
                <div class="jrchat-tab">
                    <div class="jrchat-tab-inset">{jrCore_icon icon="chat-contract"}</div>
                </div>
            </a>

            <a id="jrchat-expand-tab" onclick="jrChat_expand()" title="{jrCore_lang module="jrChat" id=2 default="make chat pane wider"}" data-state="enabled">
                <div class="jrchat-tab">
                    <div class="jrchat-tab-inset">{jrCore_icon icon="chat-expand"}</div>
                </div>
            </a>

        </div>

        <a onclick="jrChat_toggle()" title="Chat" data-state="enabled">
            <div id="jrchat-bp1" class="jrchat-tab">
                <div class="jrchat-tab-inset"><span id="jrchat-open-close">{jrCore_icon icon="chat-open"}</span></div>
                <div id="jrchat-new-bubble" style="display:none">0</div>
            </div>
        </a>

    </div>
{/if}

<div id="jrchat-room">

    <div id="jrchat-box">

        <div id="jrchat-chat">

            <div id="jrchat-room-browser" style="display:none"></div>

            <div id="jrchat-title">
                <div class="jrchat-table">

                    <div class="jrchat-table-row">
                        <div class="jrchat-table-cell center" style="width:38px;position:relative">
                            <a onclick="jrChat_user_settings()">
                                {jrCore_module_function function="jrImage_display" module="jrUser" type="user_image" item_id=$_user._user_id size="small" crop="portrait" alt=$item.user_name _v=$_user.user_image_time width=38 height=38}
                                <div class="jrchat-config">?</div>
                            </a>
                        </div>
                        <div id="jrchat-user-settings" style="display:none"></div>
                        <div class="jrchat-table-cell center">
                            <a onclick="jrChat_search_room()"><span id="jrchat-search-room">{jrCore_icon icon="search2" size=16}</span></a> &nbsp;
                            {if strlen($_room.room_title) > 0}
                                <span id="jrchat-active-title">{$_room.room_title}</span>
                            {else}
                                <span id="jrchat-active-title">{jrCore_lang module="jrChat" id=38 default="no room selected"}</span>
                            {/if}
                            &nbsp; <a onclick="jrChat_room_browser()"><span id="jrchat-select-room">{jrCore_icon icon="chat-down" size=16}</span></a>
                        </div>
                        <div class="jrchat-table-cell center" style="width:38px;position:relative">
                            <a onclick="jrChat_get_room_users()">
                                {jrCore_icon icon="chat-contact" size=38}
                                {if isset($_room.room_user_count)}
                                    <div class="jrchat-bubble">{$_room.room_user_count|default:1}</div>
                                {/if}
                            </a>
                        </div>
                        <div id="jrchat-user-control" style="display:none"></div>
                    </div>
                </div>

                <div id="jrchat-room-search" style="display:none">
                    <div style="position: relative">
                        {jrCore_lang module="jrChat" id=45 default="Search Messages" assign="sr"}
                        <input type="text" id="jrchat-search-input" class="form_text" placeholder="{$sr|jrCore_entity_string}" onkeypress="if (this.value.trim().length > 0) { if (event && event.keyCode == 13) { jrChat_search_messages(this.value); return false } else { $('#jrchat-search-reset').removeAttr('disabled'); } }"> <input type="button" id="jrchat-search-reset" value="reset" class="form_button" disabled="disabled" onclick="jrChat_search_reset()">
                    </div>
                </div>

            </div>

            <div id="jrchat-available-rooms" style="display:none"></div>

            <div id="jrchat-messages">
                <div id="jrchat-load-next-page"></div>
                {* messages load here *}
            </div>

            <div id="jrchat-new-message">
                <input id="display-room-id" type="hidden" name="display_room_id" value="{$_room.room_id|default:0}">
                <textarea id="jrchat-new-message-input" class="form_textarea" placeholder="{jrCore_lang module="jrChat" id=4 default="type message and press enter"}" onkeypress="if (event && event.keyCode == 13) { if (this.value.trim().length > 0) { event.preventDefault(); jrChat_save_message(); return false } else { event.preventDefault(); return false } }"></textarea>

                {if !jrCore_is_mobile_device() && !jrCore_is_tablet_device()}

                    {if jrCore_module_is_active('jrSmiley') && strstr(jrUser_get_profile_home_key('quota_jrCore_active_formatters'),'jrSmiley_format_string_smiley') && jrUser_get_profile_home_key('quota_jrSmiley_show_selector') == 'on'}
                    <div id="jrchat_smiley_button">
                        <a onclick="jrSmiley_drawer()">{jrCore_icon icon="smile"}</a>
                    </div>
                    {/if}
                    <div id="jrchat-upload-images" onmouseover="$('#pm_chat_file .upload_button').addClass('sprite_icon_hilighted')" onmouseout="$('#pm_chat_file .upload_button').removeClass('sprite_icon_hilighted')">
                        {jrCore_upload_button module="jrChat" field="chat_file" allowed="{$file_types}" maxsize="{$max_size}" multiple="true" upload_text="&#8679;" oncomplete="jrChat_complete_file_uploads()"}
                    </div>

                {else}
                    <div id="jrchat-mobile-send">
                        <a onclick="if ($('#jrchat-new-message-input').val().trim().length > 0) { event.preventDefault(); jrChat_save_message(); return false }">{jrCore_icon icon="chat-send"}</a>
                    </div>

                {/if}
                <span id="chi" class="ellipsis_animated-inner" style="display:none"><span>.</span><span>.</span><span>.</span></span>
            </div>

            <div id="jrchat_smiley_drawer" class="smileys-invisible"><!-- smiley drawer--></div>

        </div>

    </div>

</div>

<div id="jrchat-icon-size" style="display:none">{$icon_size}</div>
<div id="jrchat-icon-color" style="display:none">{$icon_color}</div>
<div id="jrchat-version" style="display:none">{$_mods.jrChat.module_version}</div>
<div id="jrchat-width" style="display:none">{$chat_width}</div>
<div id="jrchat-search-page" style="display:none"></div>
<div id="jrchat-controls-holder" style="display:none">
    <div class="jrchat-controls">{jrCore_icon icon="close" size=16}</div>
</div>
<div id="jrchat-beginning-holder" style="display:none">
    <div id="jrchat-page-limit">{jrCore_lang module="jrChat" id=36 default="beginning of chat"}</div>
</div>
<div id="jrchat-no-room-holder" style="display:none">
    <div id="jrchat-no-room-notice">
        {if jrUser_is_admin() || $_user.quota_jrChat_create_rooms == 'on'}
            {jrCore_lang module="jrChat" id=37 default="no chat rooms! click the icon to create one"}
        {else}
            {jrCore_lang module="jrChat" id=48 default="no chat room selected"}
        {/if}
        <br><br><a onclick="jrChat_room_browser()">{jrCore_icon icon="chat-open"}</a>
    </div>
</div>

