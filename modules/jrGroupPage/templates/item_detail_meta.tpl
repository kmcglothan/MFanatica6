{jrCore_module_url module="jrGroupPage" assign="murl"}
<meta property="og:url" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="{$item.npage_title|jrCore_entity_string}"/>
{if isset($item.npage_description)}
<meta property="og:description" content="{$item.npage_body|jrCore_strip_html|truncate:400|jrCore_entity_string}"/>
{/if}
{if $item.npage_image_size > 0}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/npage_image/{$item._item_id}/xxlarge/_v={$item.npage_image_time}" />
<meta property="og:image:width" content="{$item.npage_image_width}"/>
<meta property="og:image:height" content="{$item.npage_image_height}"/>
{/if}
<meta property="og:see_also" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$smarty.now}"/>

{* Twitter Card *}
<meta name="twitter:card" content="summary"/>
<meta name="twitter:title" content="{$item.npage_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.npage_body|jrCore_strip_html|truncate:180|jrCore_entity_string}"/>

