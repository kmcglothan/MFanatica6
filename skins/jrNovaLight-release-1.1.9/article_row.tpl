{jrCore_module_url module="jrPage" assign="murl"}
{if isset($_items)}
{foreach from=$_items item="item"}
{*this is on the main site.*}
<div class="block">
    {if jrUser_is_master() || jrUser_is_admin()}
    <div class="block_config">
        <a href="{$jamroom_url}/{jrCore_module_url module="jrPage"}/update/id={$item._item_id}">{jrCore_icon icon="gear"}</a>
    </div>
    {/if}
    <h1>{$item.page_title}</h1>
    <div class="breadcrumbs"><a href="{$jamroom_url}/articles">{jrCore_lang module="jrNovaLight" id="52" default="Archives"}</a> &raquo; {$item.page_title}</div>
</div>

<div class="block">
    <div id="jrpage_body">
        <div class="normal">
            {$item.page_body|jrCore_format_string:$item.profile_quota_id}
        </div>

        {* bring in module features *}
        {jrCore_item_detail_features module="jrPage" item=$item}

    </div>
</div>
{/foreach}

{/if}
