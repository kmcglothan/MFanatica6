A Discussion you are following has been updated:

user: {$_user.user_name}
post: {$discuss_title}
view: {$discuss_url}

Here is the message that has just been posted:

---------------------------------------

{$discuss_message}

---------------------------------------

{if $_conf.jrGroupDiscuss_follower_notification == 'default'}
There may also be other replies, but you will not receive any more notifications until you visit the discussion again.
{/if}

