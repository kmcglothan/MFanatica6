{if isset($_conf.jrNova_featured_video_ids) && strlen($_conf.jrNova_featured_video_ids) > 0}
    {jrCore_list module="jrVideo" order_by="_item_id desc" limit="4" search1="_item_id in `$_conf.jrNova_featured_video_ids`" template="index_videos_row.tpl"}
{elseif isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
    {jrCore_list module="jrVideo" order_by="video_title random" limit="4" template="index_videos_row.tpl" require_image="video_image"}
{else}
    {jrCore_list module="jrVideo" order_by="video_title random" limit="4" template="index_videos_row.tpl"}
{/if}
