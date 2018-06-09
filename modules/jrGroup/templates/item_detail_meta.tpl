{jrCore_module_url module="jrGroup" assign="murl"}
<meta property="og:url" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="{$item.group_title|jrCore_entity_string}"/>
{if isset($item.group_description)}
<meta property="og:description" content="{$item.group_description|jrCore_strip_html|truncate:400|jrCore_entity_string}"/>
{/if}
{if $item.group_image_size > 0}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/group_image/{$item._item_id}/xxlarge/_v={$item.group_image_time}" />
<meta property="og:image:width" content="{$item.group_image_width}"/>
<meta property="og:image:height" content="{$item.group_image_height}"/>
{/if}
<meta property="og:see_also" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$smarty.now}"/>

{* Twitter Card *}
<meta name="twitter:card" content="summary"/>
<meta name="twitter:title" content="{$item.group_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.group_description|jrCore_strip_html|truncate:180|jrCore_entity_string}"/>


