{jrCore_module_url module="jrVideo" assign="murl"}
<meta property="og:url" content="{$current_url}" />
<meta property="og:type" content="movie" />
<meta property="og:title" content="{$item.video_title|jrCore_entity_string}" />
{if isset($item.video_description)}
<meta property="og:description" content="{$item.video_description|jrCore_entity_string}" />
{/if}
{if $item.video_image_size > 0}
<meta property="og:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/video_image/{$item._item_id}/xxlarge/_v={$item.video_image_time}" />
<meta property="og:image:width" content="{$item.video_image_width}"/>
<meta property="og:image:height" content="{$item.video_image_height}"/>
{/if}
<meta property="og:site_name" content="{$_conf.jrCore_system_name}" />
<meta property="og:updated_time" content="{$smarty.now}" />

{* twitter card here *}
<meta name="twitter:card" content="player" />
{*<meta name="twitter:site" content="@rchoi" />*}
<meta name="twitter:title" content="{$item.video_title|jrCore_entity_string}" />
<meta name="twitter:description" content="{$item.video_description|jrCore_strip_html|truncate:180|jrCore_entity_string}" />
{if $item.video_image_size > 0}
<meta name="twitter:image" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/image/video_image/{$item._item_id}/xxlarge/_v={$item.video_image_time}" />
{/if}
<meta name="twitter:player" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/embed/{$item._item_id}/{$item.video_title_url}" />
<meta name="twitter:player:width" content="{$item.video_file_resolution_width}" />
<meta name="twitter:player:height" content="{$item.video_file_resolution_height}" />
<meta name="twitter:player:stream" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/stream/video_file/1/key=[jrCore_media_play_key]/file.{$item.video_file_extension}" />
<meta name="twitter:player:stream:content_type" content="video/flv" />



