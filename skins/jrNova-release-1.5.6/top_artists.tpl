{if isset($_conf.jrNova_artist_quota) && $_conf.jrNova_artist_quota > 0}
    {if isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" search1="profile_quota_id in `$_conf.jrNova_artist_quota`" template="index_list_profiles.tpl" require_image="profile_image" pagebreak="5" page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" search1="profile_quota_id in `$_conf.jrNova_artist_quota`" template="index_list_profiles.tpl" pagebreak="5" page=$_post.p}
    {/if}
{else}
    {if isset($_conf.jrNova_require_images) && $_conf.jrNova_require_images == 'on'}
        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="index_list_profiles.tpl" require_image="profile_image" pagebreak="5" page=$_post.p}
    {else}
        {jrCore_list module="jrProfile" order_by="profile_view_count NUMERICAL_DESC" template="index_list_profiles.tpl" pagebreak="5" page=$_post.p}
    {/if}
{/if}
