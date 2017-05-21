{if $happened}
    {if $attendee}
        {$cls = 'attendee'}
        {jrCore_lang module="jrEvent" id=37 default="Attended" assign="val"}
        {$val = "&#10003; `$val`"}
    {else}
        {$cls = 'nonattendee'}
        {jrCore_lang module="jrEvent" id=36 default="Attended?" assign="val"}
    {/if}
{else}
    {if $attendee}
        {$cls = 'attendee'}
        {jrCore_lang module="jrEvent" id=35 default="Attending" assign="val"}
        {$val = "&#10003; `$val`"}
    {else}
        {$cls = 'nonattendee'}
        {jrCore_lang module="jrEvent" id=34 default="Attending?" assign="val"}
    {/if}
{/if}

<input type="button" id="attend{$_item_id}" name="attend{$_item_id}" class="form_button event_attend_button {$cls}" value="{$val|jrCore_entity_string}" title="{$val|jrCore_entity_string}" onclick="jrEventAttend({$_item_id})">
