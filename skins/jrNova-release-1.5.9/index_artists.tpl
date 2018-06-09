{if isset($_conf.jrNova_featured_artist_ids) && strlen($_conf.jrNova_featured_artist_ids) > 0}
    {jrCore_list module="jrProfile" order_by="_profile_id asc" quota_id=$_conf.jrNova_artist_quota limit="4" search1="_profile_id in `$_conf.jrNova_featured_artist_ids`" search2="profile_active = 1" template="index_artists_row.tpl"}
{elseif isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by="_profile_id random" limit="4" quota_id=$_conf.jrNova_artist_quota search1="profile_active = 1" template="index_artists_row.tpl" require_image="profile_image"}
{else}
    {jrCore_list module="jrProfile" order_by="_profile_id random" limit="4" quota_id=$_conf.jrNova_artist_quota search1="profile_active = 1" template="index_artists_row.tpl"}
{/if}
