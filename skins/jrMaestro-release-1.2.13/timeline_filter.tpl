
{if isset($_items)}
    {foreach from=$_items item="item"}
        {jrCore_module_url module="jrAction" assign="murl"}
        {jrCore_module_url module=$item.action_module assign="aurl"}

        {$name = $aurl}
        {if $item.action_module == 'jrAction'}
            {jrCore_lang id=70 skin="jrMaestro" default="Status" assign="name"}
        {/if}

        {if strlen($name) > 0}
            <li onclick="jrCore_window_location('{$jamroom_url}/{$item.profile_url}/{$murl}/{$aurl}')" class="{$item.action_module}">
                <a href="{$jamroom_url}/{$item.profile_url}/{$murl}/{$aurl}">{$name|replace:"_":" "}</a>
            </li>
        {/if}
    {/foreach}
{/if}