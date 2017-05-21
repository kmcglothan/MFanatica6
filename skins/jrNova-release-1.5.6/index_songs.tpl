{if isset($_conf.jrNova_featured_song_ids) && strlen($_conf.jrNova_featured_song_ids) > 0}
    {jrCore_list module="jrAudio" order_by="_item_id desc" limit="4" search1="_item_id in `$_conf.jrNova_featured_song_ids`" template="index_songs_row.tpl"}
{elseif isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
    {jrCore_list module="jrAudio" order_by="audio_title random" limit="4" template="index_songs_row.tpl" require_image="audio_image"}
{else}
    {jrCore_list module="jrAudio" order_by="audio_title random" limit="4" template="index_songs_row.tpl"}
{/if}
