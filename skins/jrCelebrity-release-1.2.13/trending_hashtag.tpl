{jrCore_module_url module="jrAction" assign="murl"}

{if isset($_items)}
    {foreach from=$_items item="item"}
        <p><a class="hashtag" href="{$jamroom_url}/{$murl}/ss={$item.hash_text}">#{$item.hash_text}</a><span>{$item.hash_count|jrCore_number_format} {jrCore_lang skin="jrCelebrity" id="121" default="posts"}</span></p>
    {/foreach}
{/if}

