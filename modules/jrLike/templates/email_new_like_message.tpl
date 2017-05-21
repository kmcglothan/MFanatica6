{if $like_action == 'like'}
User '{$like_user_name}' has liked "<a href="{$like_url}">{$like_title}</a>"
    {else}
User '{$like_user_name}' has disliked "<a href="{$like_url}">{$like_title}</a>"
{/if}

{$like_url}
