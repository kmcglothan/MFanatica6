{jrCore_module_url module="jrFile" assign="murl"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:see_also" content="{$og_item_url|replace:"http:":"`$method`:"}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$item.file_title|jrCore_entity_string}" />
{if isset($item.file_image_size) && $item.file_image_size > 100}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/file_image/{$item._item_id}/xxlarge/_v={$item.file_image_time}" />
<meta property="og:image:width" content="256" />
<meta property="og:image:height" content="256" />
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrFile/facebook_shared_icon.png" />
<meta property="og:image:width" content="256" />
<meta property="og:image:height" content="256" />
{/if}
<meta property="og:description" content="{$item.file_file_name|jrCore_strip_html|truncate:200|jrCore_entity_string}" />
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$item._updated}" />
