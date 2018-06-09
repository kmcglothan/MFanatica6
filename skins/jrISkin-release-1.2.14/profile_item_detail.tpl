{if $profile_disable_sidebar != 1  && strpos($current_url, $_conf.jrISkin_forum_profile) !== 0}
{jrCore_include template="profile_sidebar.tpl"}
<div class="col8 last">
    {else}
    <div class="col12 last">
        {/if}
    <div>
        {$profile_item_detail_content}
    </div>
</div>


