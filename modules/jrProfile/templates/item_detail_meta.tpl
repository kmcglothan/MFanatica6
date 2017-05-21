{jrCore_module_url module="jrProfile" assign="murl"}
{assign var="title" value="`$item.profile_name` (@`$item.profile_url`)"}
<meta property="og:url" content="{$current_url|replace:"http:":"`$method`:"}" />
<meta property="og:see_also" content="{$jamroom_url|replace:"http:":"`$method`:"}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$title|jrCore_entity_string}" />
{if isset($item.profile_bio) && strlen($item.profile_bio) > 0}
<meta property="og:description" content="{$item.profile_bio|jrCore_strip_html|jrCore_entity_string|truncate:300}" />
{/if}
<meta property="og:image" content="{$jamroom_url}/{$murl}/image/profile_image/{$item._profile_id}/xxlarge" />
<meta property="og:image:width" content="512" />
<meta property="og:image:height" content="385" />
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />
