{jrCore_module_url module="jrEvent" assign="murl"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$item.event_title|jrCore_entity_string}" />
{if isset($item.event_image_size) && $item.event_image_size > 100}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/event_image/{$item._item_id}/xxlarge/_v={$item.event_image_time}" />
<meta property="og:image:width" content="512" />
<meta property="og:image:height" content="385" />
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrEvent/facebook_shared_icon.png" />
<meta property="og:image:width" content="256" />
<meta property="og:image:height" content="256" />
{/if}
<meta property="og:description" content="{$item.event_date|jrCore_date_format:"%A %B %e %Y, %l:%M %p":false}{if $item.event_end_day} {jrCore_lang module="jrEvent" id="64" default="-"} {$item.event_end_day|jrCore_date_format:"%A %B %e %Y, %l:%M %p":false}{/if} {$item.event_description|jrCore_strip_html|truncate:200|jrCore_entity_string}" />
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$item._updated}" />
