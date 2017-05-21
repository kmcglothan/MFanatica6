{if isset($item.action_text)}
{assign var="title" value="`$item.profile_name`: Activity update"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="website" />
<meta property="og:title" content="{$page_title|jrCore_entity_string}" />
<meta property="og:description" content="{$item.action_text|jrCore_entity_string}" />
{if jrCore_checktype($item.profile_image_size, 'number_nz')}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrProfile"}/image/profile_image/{$item._profile_id}/xxlarge" />
<meta property="og:image:width" content="480"/>
<meta property="og:image:height" content="360"/>
{/if}
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />
{/if}

{* Twitter Card *}
<meta name="twitter:title" content="{$page_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.action_text|jrCore_strip_html|truncate:180|jrCore_entity_string}"/>
{if jrCore_checktype($item.profile_image_size, 'number_nz')}
<meta name="twitter:image" content="{$jamroom_url}/{jrCore_module_url module="jrProfile"}/image/profile_image/{$item._profile_id}/xxlarge/_v={$item.profile_image_time}"/>
<meta name="twitter:image:src" content="{$jamroom_url}/{jrCore_module_url module="jrProfile"}/image/profile_image/{$item._profile_id}/xxlarge/_v={$item.profile_image_time}"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:image:alt" content="{$item.action_text|jrCore_entity_string}"/>
{/if}