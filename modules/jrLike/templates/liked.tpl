{if isset($_items)}
    <h1>{jrCore_lang module="jrLike" id=17 default="Liked Items"}</h1>
    <br><br>
    {foreach $_items as $item}
        {$pfx = jrCore_db_get_prefix($item.like_module)}
        {$item.like_module}: "{$item["`$pfx`_title"]}"
        <br>
    {/foreach}
{/if}

