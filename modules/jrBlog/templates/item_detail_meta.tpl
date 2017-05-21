{jrCore_module_url module="jrBlog" assign="murl"}

{* Facebook Card *}
<meta property="og:url" content="{$current_url}"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="{$item.blog_title|jrCore_entity_string}"/>
{if isset($item.blog_image_size) && $item.blog_image_size > 100}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/blog_image/{$item._item_id}/xxlarge/_v={$item.blog_image_time}"/>
<meta property="og:image:width" content="512"/>
<meta property="og:image:height" content="385"/>
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrBlog/facebook_shared_icon.png"/>
<meta property="og:image:width" content="256"/>
<meta property="og:image:height" content="256"/>
{/if}
<meta property="og:description" content="{$item.blog_text|jrCore_strip_html|truncate:400|jrCore_entity_string}"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$item._updated}"/>

{* Twitter Card *}
<meta name="twitter:title" content="{$item.blog_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.blog_text|jrCore_strip_html|truncate:180|jrCore_entity_string}"/>
{if isset($item.blog_image_size) && $item.blog_image_size > 100}
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/blog_image/{$item._item_id}/xxlarge/_v={$item.blog_image_time}"/>
<meta name="twitter:image:src" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/blog_image/{$item._item_id}/xxlarge/_v={$item.blog_image_time}"/>
<meta name="twitter:image:alt" content="{$item.blog_title|jrCore_entity_string}"/>
{else}
<meta name="twitter:card" content="summary"/>
{/if}
