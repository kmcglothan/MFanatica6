{jrCore_module_url module="jrEvent" assign="murl"}
<div class="block">

    <div class="title">
        <div class="block_config">
            {jrCore_item_detail_buttons module="jrEvent" item=$item}
        </div>
        <h1>{$item.event_title}</h1>

        <div class="breadcrumbs">
            <a href="{$jamroom_url}/{$item.profile_url}/">{$item.profile_name}</a> &raquo;
            <a href="{$jamroom_url}/{$item.profile_url}/{$murl}">{jrCore_lang module="jrEvent" id="31" default="Event"}</a> &raquo; {$_post._2|default:"Event"}
        </div>
    </div>

    <div class="block_content">
        <div class="item">
            <div class="container">
                <div class="row">
                    <div class="col3">
                        <div class="block_image center">
                        {jrCore_module_function function="jrImage_display" module="jrEvent" type="event_image" item_id=$item._item_id size="large" alt=$item.event_title width=false height=false class="iloutline img_scale"}
                        <br><br>
                        {jrCore_module_function function="jrRating_form" type="star" module="jrEvent" index="1" item_id=$item._item_id current=$item.event_rating_1_average_count|default:0 votes=$item.event_rating_1_count|default:0}
                        </div>
                    </div>
                    <div class="col9 last">
                        <div class="p10">
                            <strong>{$item.event_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p":false}{if $item.event_end_day} {jrCore_lang module="jrEvent" id="64" default="-"} {$item.event_end_day|jrCore_date_format:"%A %B %e %Y, %l:%M %p":false}{/if}</strong>

                            {if isset($item.event_location) && strlen($item.event_location) > 1}
                                <br><span class="normal">@ {$item.event_location|truncate:60}</span>
                            {/if}

                            {if $item.quota_jrEvent_allowed_attending == 'on' && isset($item.event_attendee) && is_array($item.event_attendee) && isset($item.event_attendee_count) && $item.event_attendee_count > 0}
                                {assign var="attendees" value=""}
                                {foreach from=$item.event_attendee item="attendee"}
                                    {assign var="attendees" value="`$attendees`&nbsp;<a href='`$jamroom_url`/`$attendee.profile_url`'>@`$attendee.user_name`</a>,"}
                                {/foreach}
                                <br>
                                <span class="normal">{jrCore_lang module="jrEvent" id="38" default="Attendees"}: {$attendees|substr:0:-1}</span>
                            {/if}

                            <br>{$item.event_description|jrCore_format_string:$item.profile_quota_id}

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrEvent" item=$item}

    </div>

</div>
