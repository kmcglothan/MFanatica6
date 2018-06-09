{jrCore_module_url module="jrAudio" assign="murl"}
{jrCore_image module="jrAudio" image="facebook_player_skin.jpg" src_only=true assign="skin_url"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="movie" />
<meta property="og:title" content="{$item.audio_title|jrCore_entity_string}" />
{if isset($item.audio_description)}
{assign var="title" value="`$profile_name`: `$item.audio_title`"}
<meta property="og:description" content="{$item.audio_description|jrCore_entity_string}" />
{else}
<meta property="og:description" content="by {$item.profile_name|jrCore_entity_string}" />
{/if}
{if $item.audio_image_size > 0}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/audio_image/{$item._item_id}/xxlarge/_v={$item.audio_image_time}" />
{else}
<meta property="og:image" content="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/module/jrAudio/facebook_shared_icon.png" />
{/if}
<meta property="og:image:width" content="512"/>
<meta property="og:image:height" content="385"/>
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />

{* twitter card here *}
<meta name="twitter:card" content="player" />
{*<meta name="twitter:site" content="@jamroom" />*}
<meta name="twitter:title" content="{$item.audio_title|jrCore_entity_string}" />
{if isset($item.audio_description)}
<meta name="twitter:description" content="{$item.audio_description|jrCore_strip_html|truncate:180|jrCore_entity_string}" />
{/if}
{if $item.audio_image_size > 0}
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/audio_image/{$item._item_id}/xxlarge/_v={$item.audio_image_time}" />
{/if}
<meta name="twitter:player" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/embed/{$item._item_id}/{$item.audio_title_url}" />
<meta name="twitter:player:width" content="400" />
<meta name="twitter:player:height" content="215" />

