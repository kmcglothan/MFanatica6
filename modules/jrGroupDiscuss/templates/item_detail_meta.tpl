<meta property="og:url" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="{$item.discuss_title|jrCore_entity_string}"/>
<meta property="og:description" content="{$item.discuss_description|jrCore_strip_html|truncate:400|jrCore_entity_string}"/>
<meta property="og:see_also" content="{$current_url|replace:"http:":"`$method`:"}"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$smarty.now}"/>

{* Twitter Card *}
<meta name="twitter:card" content="summary"/>
<meta name="twitter:title" content="{$item.discuss_title|jrCore_entity_string}"/>
<meta name="twitter:description" content="{$item.discuss_description|jrCore_strip_html|truncate:180|jrCore_entity_string}"/>
