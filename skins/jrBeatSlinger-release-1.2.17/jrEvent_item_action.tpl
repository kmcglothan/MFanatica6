{jrCore_module_url module="jrEvent" assign="murl"}
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

        {if $item.action_mode == 'create'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.page_title_url}">
                    {jrCore_lang module="jrEvent" id="32" default="Created a new Event"}.
                </a></span><br>
        {elseif $item.action_mode == 'update'}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.page_title_url}">
                    {jrCore_lang module="jrEvent" id="33" default="Updated an Event"}.
                </a></span><br>
        {else}
            <span class="action_desc"><a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.page_title_url}">
                    {jrCore_lang module="jrEvent" id="143" default="Is attending an event"}.
                </a></span><br>
        {/if}

        <span class="action_time">{$item._created|jrCore_date_format:"relative"}</span>

    </div>
</div>
<div class="item_media event">
    <div>
        <div class="wrap clearfix">
            {if strlen($item.action_data.event_image_size) > 0}
                <div class="media_image">
                    <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$item.action_item_id}/{$item.action_data.event_title_url}"
                       title="{$item.action_data.event_title|jrCore_entity_string}">
                        {jrCore_module_function
                        function="jrImage_display"
                        module="jrEvent"
                        type="event_image"
                        item_id=$item.action_item_id
                        size="xlarge"
                        crop="4:3"
                        class="img_scale"
                        alt=$item.action_data.event_title
                        }
                    </a>
                </div>
            {/if}
            <span class="title">{$item.action_data.event_title|truncate:60}</span>
            <span class="location">{$item.action_data.event_location|jrCore_strip_html|truncate:60}</span>
            <span class="date">{$item.action_data.event_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p"}</span>
            <div class="media_text">
                    <span id="truncated_event_{$item.action_item_id}">
               <p>
                   {$item.action_data.event_description|jrCore_strip_html|truncate:400}
                   {if strlen($item.action_data.event_description) > 400}
                       <span class="more"><a href="#" onclick="showMore('event_{$item._item_id}')">More</a></span>
                   {/if}
               </p>
            </span>
            <span id="full_event_{$item._item_id}" style="display: none;"><p>
                    {$item.action_data.event_description|jrCore_strip_html}
                    <span class="more"><a href="#"
                                          onclick="showMore('event_{$item._item_id}')">Less</a></span>
                </p></span>
            </div>

            <p><span class="attending">{jrCore_lang module="jrEvent" id="38" default="Attendees"}
                    {if isset($item)}
                        {$_item = jrCore_db_get_item('jrEvent', $item.action_item_id)}
                    {/if}
                    : {$_item.event_attendee_count|default:0}</span>
                {jrEvent_attending_button item=$_item}
            </p>
        </div>
    </div>
</div>





