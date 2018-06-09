<!doctype html>
<html lang="{jrCore_lang module="_settings" id="lang" default="en"}" dir="{jrCore_lang module="_settings" id="direction" default="ltr"}">
<head>
<title>{if isset($page_title) && strlen($page_title) > 0}{$page_title}{else}{jrCore_lang skin=$_conf.jrCore_active_skin id="1" default="Home"}{/if} | {$_conf.jrCore_system_name}</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
{if isset($meta)}
{foreach from=$meta key="mname" item="mvalue"}
<meta name="{$mname}" content="{$mvalue}" />
{/foreach}
{/if}
<link rel="stylesheet" href="{jrCore_css_src}" media="screen" />
{if isset($css_href)}
{foreach from=$css_href item="_css"}
<link rel="stylesheet" href="{$_css.source}" media="{$_css.media|default:"screen"}" />
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
{if isset($selected) && ($selected == 'radio_player' || $selected == 'channel_player')}
<style type="text/css">
    #jrchat-tabs {
        display: none !important;
    }
</style>
{/if}
</head>