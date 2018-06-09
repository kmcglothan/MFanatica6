<div class="p5">
    <span class="action_item_title">
    {if $item.action_mode == 'login'}&nbsp;
        <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a> {jrCore_lang module="jrUser" id="127" default="has logged in"}
    {elseif $item.action_mode == 'signup'}
        <a href="{$jamroom_url}/{$item.profile_url}">@{$item.user_name}</a> {jrCore_lang module="jrUser" id="126" default="has signed up"}
    {/if}
    </span>
</div>
