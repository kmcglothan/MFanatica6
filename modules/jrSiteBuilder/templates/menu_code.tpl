{*{$menu_code|jrCore_format_string_bbcode}*}

[code]
{foreach $_menu as $item}
{if isset($item.parent_url)}
{ldelim}jrSiteBuilder_default_menu title="{$item.menu_title}" url="{$item.menu_url}" parent="{$item.parent_url}" weight="{$item.menu_order}"}
{else}
{ldelim}jrSiteBuilder_default_menu title="{$item.menu_title}" url="{$item.menu_url}" weight="{$item.menu_order}"}
{/if}
{/foreach}
{ldelim}jrSiteBuilder_menu}
[/code]
