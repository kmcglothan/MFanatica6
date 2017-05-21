{jrCore_module_url module="jrProfile" assign="murl"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$item.soundcloud_title|jrCore_entity_string}" />
{if isset($og_description)}
<meta property="og:description" content="{$item.soundcloud_description|jrCore_entity_string}" />
{/if}
<meta property="og:image" content="{$item.soundcloud_artwork_url}" />
<meta property="og:image:width" content="512" />
<meta property="og:image:height" content="385" />
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />