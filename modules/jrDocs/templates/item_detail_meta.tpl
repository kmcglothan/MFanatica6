{jrCore_module_url module="jrImage" assign="murl"}
{$description = ''}
{if isset($_items) && is_array($_items)}
    {foreach $_items as $item}{* find the overview, use it as a description *}
        {if strtolower($item.doc_title) == "overview"}
            {$description =  {$item.doc_content|jrCore_format_string:0|jrCore_strip_html|truncate:400|jrCore_entity_string}}
        {/if}
    {/foreach}
{/if}
<meta property="og:url" content="{$current_url}"/>
<meta property="og:type" content="website"/>
<meta property="og:title" content="{$page_title|jrCore_entity_string}"/>
<meta property="og:description" content="{$description}"/>
<meta property="og:image" content="{$jamroom_url}/{$murl}/img/module/jrDocs/facebook_shared_icon.png"/>
<meta property="og:image:width" content="256"/>
<meta property="og:image:height" content="256"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}"/>
<meta property="og:updated_time" content="{$smarty.now}"/>
