{if isset($_conf.jrNovaLight_require_images) && $_conf.jrNovaLight_require_images == 'on'}
    {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="7" template="index_list_songs.tpl" require_image="audio_image" pagebreak="5" page=$_post.p}
{else}
    {jrCore_list module="jrAudio" chart_field="audio_file_stream_count" chart_days="7" template="index_list_songs.tpl" pagebreak="5" page=$_post.p}
{/if}
