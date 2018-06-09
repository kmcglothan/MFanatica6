{if isset($_items)}
    {jrCore_module_url module="jrAudio" assign="murl"}
    {foreach from=$_items item="item"}
        {if isset($item.audio_genre) && strlen($item.audio_genre) > 0}
            <option value="{$item.audio_genre}">{$item.audio_genre}</option>
        {/if}
    {/foreach}
{/if}
