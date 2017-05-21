A new response has been received for the {$form_title} form:

{if jrUser_is_logged_in()}
Submitted By:
user: {$_user.user_name}
email: {$_user.user_email}
{/if}

{if is_array($_save)}
Submitted Form Information:
{foreach $_save as $k => $v}
{$k}: {$v|strip_tags}
{/foreach}
{/if}

This response has been saved to the Custom Form DataStore:
{$form_browser_url}
