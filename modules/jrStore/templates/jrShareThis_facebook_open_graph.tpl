{jrCore_module_url module="jrStore" assign="murl"}
{jrCore_module_url module="jrShareThis" assign="surl"}
{*<!-- Facebook OG -->*}
<meta id="ogtitle" property="og:title" content="{$og_title}"/>
<meta property="og:type" content="article"/>
<meta id="ogurl" property="og:url" content="{$og_item_url|replace:"http:":"`$method`:"}"/>
{if strlen($og_image_url) > 0}
<meta id="ogimage" property="og:image" content="{$og_image_url|replace:"http:":"`$method`:"}" />
<meta id="ogimagewidth" property="og:image:width" content="800"/>
<meta id="ogimageheigth" property="og:image:height" content="800"/>
{/if}
<meta id="ogdescription" property="og:description" content="{$og_body|jrCore_strip_html|jrCore_entity_string|truncate:200}"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />

