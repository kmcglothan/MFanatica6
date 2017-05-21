{if isset($_items) && is_array($_items)}
    {foreach $_items as $item}
        {if $item.user_is_online == '1'}
            {$online = '1'}
        {/if}
        {if jrCore_checktype($item['user_birthdate'], 'number_nz')}
            <span>{jrCore_icon icon="birthday" size="16"} {jrCore_lang skin="jrElastic2" id=21 default="Birthday"} {$item.user_birthdate|jrCore_date_birthday_format:"%B %d"}</span>
        {/if}
    {/foreach}
    {if $online == '1'}
         <span class="online_status"> {jrCore_icon icon="online" size="16"} {jrCore_lang module="jrUser" id="101" default="online"}</span>
    {else}
        <span class="online_status">{jrCore_icon icon="online" size="16" color="CCCCCC"} {jrCore_lang module="jrUser" id="102" default="offline"}</span>
    {/if}
{/if}