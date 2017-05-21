<!doctype html>
<html lang="{jrCore_lang module="_settings" id="lang" default="en"}" dir="{jrCore_lang module="_settings" id="direction" default="ltr"}">
<head>{jrCore_lang skin="jrBeatSlinger" id="1" assign="default_title"}
<title>{$page_title|default:"`$default_title`"|capitalize} | {$_conf.jrCore_system_name}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
{if isset($meta)}
{foreach from=$meta key="mname" item="mvalue"}
<meta name="{$mname}" content="{$mvalue}">
{/foreach}
{/if}
<link rel="stylesheet" href="{jrCore_server_protocol}://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,700italic,400,600,700" type="text/css">
<link rel="stylesheet" href="{jrCore_css_src}" media="screen" type="text/css">
{if isset($css_href)}
{foreach from=$css_href item="_css"}
<link rel="stylesheet" href="{$_css.source}" media="{$_css.media|default:"screen"}" type="text/css">
{/foreach}
{/if}
{if isset($css_embed)}
<style type="text/css">
{$css_embed}</style>
{/if}
{if isset($javascript_embed)}
<script type="text/javascript">
{$javascript_embed}</script>
{/if}
<script type="text/javascript" src="{jrCore_javascript_src}"></script>
{if isset($javascript_href)}
{foreach from=$javascript_href item="_js"}
<script type="{$_js.type|default:"text/javascript"}" src="{$_js.source}"></script>
{/foreach}
{/if}
{if isset($javascript_ready_function)}
<script type="text/javascript">
$(document).ready(function(){
{$javascript_ready_function}return true;
});
</script>
{/if}

</head>
