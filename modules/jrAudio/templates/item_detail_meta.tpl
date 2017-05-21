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
{if $item.quota_jrAudio_allow_player == 'on'}
<meta property="og:video" content="{$jamroom_url|replace:"http:":"https:"}/modules/jrAudio/contrib/flash/multi.swf?width=400&amp;height=120&amp;autoplay=1&amp;title={$item.audio_title|urlencode}&amp;mp3={$jamroom_url|replace:"http:":"https:"|urlencode}%2F{$murl}%2Fstream%2Faudio_file%2F{$item._item_id}%2Ffile.mp3&amp;skin={$skin_url|replace:"http:":"https:"|urlencode}" />
<meta property="og:video:width" content="400" />
<meta property="og:video:height" content="120" />
<meta property="og:video:type" content="application/x-shockwave-flash" />
<meta property="og:video:secure_url" content="{$jamroom_url|replace:"http:":"https:"}/modules/jrAudio/contrib/flash/multi.swf?width=400&amp;height=120&amp;autoplay=1&amp;title={$item.audio_title|urlencode}&amp;mp3={$jamroom_url|replace:"http:":"https:"|urlencode}%2F{$murl}%2Fstream%2Faudio_file%2F{$item._item_id}%2Ffile.mp3&amp;skin={$skin_url|replace:"http:":"https:"|urlencode}" />
{/if}


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
{if $item.quota_jrAudio_allow_player == 'on'}
<meta name="twitter:player" content="{$jamroom_url|replace:"http:":"`$method`:"}/{$murl}/embed/{$item._item_id}/{$item.audio_title_url}" />
<meta name="twitter:player:width" content="400" />
<meta name="twitter:player:height" content="215" />
{/if}

