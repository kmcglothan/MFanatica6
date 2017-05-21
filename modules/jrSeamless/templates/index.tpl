{jrCore_include template="header.tpl"}
{if isset($_post.option) && $_post.option != ''}
    {jrCore_array name="_list" value=$_post.option explode="yes" separator="%2C"}
    {foreach from=$_list item="list"}
        {foreach from=$_mods key="k" item="_v"}
            {if $_v.module_prefix == $list}
                {jrCore_array name="_smods" key=$k value=$k}
            {/if}
        {/foreach}
    {/foreach}
{/if}
{jrSeamless_list modules=$_smods|implode:"," order_by="_created NUMERICAL_DESC" pagebreak="10" page=$_post.p pager=true}
{jrCore_include template="footer.tpl"}
