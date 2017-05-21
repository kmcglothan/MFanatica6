{if isset($_post.option) && $_post.option == 'alpha'}
    {assign var="order_by" value="profile_name asc"}
    {assign var="jrload_div" value="alpha_members"}
    {assign var="pb" value="8"}
{elseif isset($_post.option) && $_post.option == 'newest'}
    {assign var="order_by" value="_created desc"}
    {assign var="jrload_div" value="new_members"}
    {assign var="pb" value="8"}
{elseif isset($_post.option) && $_post.option == 'most_viewed'}
    {assign var="order_by" value="profile_view_count numerical_desc"}
    {assign var="jrload_div" value="most_viewed_members"}
    {assign var="pb" value="12"}
{/if}


{if isset($_conf.jrProJamLight_require_images) && $_conf.jrProJamLight_require_images == 'on'}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$_conf.jrProJamLight_member_quota require_image="profile_image" template="members_row.tpl" pagebreak=$pb page=$_post.p}
{else}
    {jrCore_list module="jrProfile" order_by=$order_by search1="profile_active = 1" quota_id=$_conf.jrProJamLight_member_quota template="members_row.tpl" pagebreak=$pb page=$_post.p}
{/if}
