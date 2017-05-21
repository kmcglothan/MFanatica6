{if isset($_items)}
{foreach $_items as $item}
    {if is_file("`$jamroom_dir`/modules/`$item.seamless_module_name`/templates/item_list.tpl")}
        {jrSeamless_parse_template item=$item template="item_list.tpl" module=$item.seamless_module_name}
    {elseif jrUser_is_admin()}
        item_list.tpl for {$item.seamless_module_name} not found<br>
    {/if}
{/foreach}
{/if}
