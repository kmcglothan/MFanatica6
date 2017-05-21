{jrCore_module_url module="jrPlaylist" assign="murl"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="movie" />
<meta property="og:title" content="{$item.playlist_title|jrCore_entity_string}" />
{if isset($item.playlist_description)}
<meta property="og:description" content="{$item.playlist_description|jrCore_entity_string}" />
{/if}
{if $item.playlist_image_size > 0}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/playlist_image/{$item._item_id}/xxlarge/_v={$item.playlist_image_time}" />
<meta property="og:image:width" content="{$item.playlist_image_width}"/>
<meta property="og:image:height" content="{$item.playlist_image_height}"/>
{/if}
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />

{* twitter card here *}
<meta name="twitter:card" content="player" />
{*<meta name="twitter:site" content="@jamroom" />*}
<meta name="twitter:title" content="{$item.playlist_title|jrCore_entity_string}" />
{if isset($item.playlist_description)}
<meta name="twitter:description" content="{$item.playlist_description|jrCore_strip_html|truncate:180|jrCore_entity_string}" />
{/if}
{if $item.playlist_image_size > 0}
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/playlist_image/{$item._item_id}/xxlarge/_v={$item.playlist_image_time}" />
{/if}
<meta name="twitter:player" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/embed/{$item._item_id}/{$item.audio_title_url}" />
<meta name="twitter:player:width" content="400" />
<meta name="twitter:player:height" content="300" />