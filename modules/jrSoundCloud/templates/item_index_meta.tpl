{jrCore_module_url module="jrProfile" assign="purl"}

{* Facebook Card *}
<meta property="og:url" content="{$current_url}"/>
<meta property="og:type" content="article"/>
<meta property="og:title" content="SoundCloud - {$item.profile_name|jrCore_entity_string}"/>
<meta property="og:description" content="Check out the full list here"/>
{if isset($item.profile_image_size) && $item.profile_image_size > 100}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$purl}/image/profile_image/{$item._item_id}/xxlarge/_v={$item.profile_image_time}"/>
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrSoundCloud/facebook_shared_icon.png"/>
{/if}
<meta property="og:image:width" content="256"/>
<meta property="og:image:height" content="256"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$item._updated}"/>

{* Twitter Card *}
<meta name="twitter:title" content="{$item.profile_name|jrCore_entity_string}"/>
<meta name="twitter:description" content="Check out the full list here"/>
{if isset($item.profile_image_size) && $item.profile_image_size > 100}
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$purl}/image/profile_image/{$item._item_id}/xxlarge/_v={$item.profile_image_time}"/>
<meta name="twitter:image:src" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$purl}/image/profile_image/{$item._item_id}/xxlarge/_v={$item.profile_image_time}"/>
<meta name="twitter:image:alt" content="{$item.profile_name|jrCore_entity_string}"/>
{else}
<meta name="twitter:card" content="summary"/>
{/if}
