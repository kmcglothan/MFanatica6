{jrCore_module_url module="jrPage" assign="murl"}

<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$item.page_title|jrCore_entity_string}" />
<meta property="og:description" content="{$item.page_body|jrCore_strip_html|truncate:200|jrCore_entity_string}" />
{if isset($item.page_image_size) && $item.page_image_size > 100}
<meta property="og:image content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/page_image/{$item._item_id}/xxlarge/_v={$item.page_image_time}"/>
<meta property="og:image:width" content="512"/>
<meta property="og:image:height" content="385"/>
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrPage/facebook_shared_icon.png" />
<meta property="og:image:width" content="256" />
<meta property="og:image:height" content="256" />
{/if}
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />

{* Twitter Card *}
<meta name="twitter:title" content="{$item.page_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.page_body|jrCore_strip_html|truncate:200|jrCore_entity_string}"/>
{if isset($item.page_image_size) && $item.page_image_size > 100}
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/page_image/{$item._item_id}/xxlarge/_v={$item.page_image_time}"/>
<meta name="twitter:image:src" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/page_image/{$item._item_id}/xxlarge/_v={$item.page_image_time}"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:image:alt" content="{$item.page_title|jrCore_entity_string}"/>
{else}
<meta name="twitter:card" content="summary"/>
{/if}
