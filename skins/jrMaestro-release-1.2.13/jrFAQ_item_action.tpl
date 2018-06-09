{jrCore_module_url module="jrFAQ" assign="murl"}

<div class="action_info">
    <div class="action_user_image" onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}')">
        {jrCore_module_function
        function="jrImage_display"
        module="jrUser"
        type="user_image"
        item_id=$item._user_id
        size="icon"
        crop="auto"
        alt=$item.user_name
        }
    </div>
    <div class="action_data">
        <div class="action_delete">
            {jrCore_item_delete_button module="jrAction" profile_id=$item._profile_id item_id=$item._item_id}
        </div>
        <span class="action_user_name"><a href="{$jamroom_url}/{$item.profile_url}" title="{$item.profile_name|jrCore_entity_string}">{$item.profile_url}</a></span>

        {if $item_mode == 'create'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.faq_question_url}">
                    {jrCore_lang module="jrFAQ" id="11" default="Posted a new faq"}.</a></span><br>
        {else}

            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item._item_id}/{$item.faq_question_url}" title="{$item.faq_question|jrCore_entity_string}">
                {jrCore_lang module="jrFAQ" id="12" default="Updated a faq"}.</a><br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>

<div class="item_media">
    <div class="wrap">
        <span class="action_item_title"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}"
                                           title="{$item.action_data.faq_question|jrCore_entity_string}">{$item.action_data.faq_question}</a></span>
    </div>
</div>
