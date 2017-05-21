{jrCore_module_url module="jrGroup" assign="murl"}
Hello {$_group.user_name} -

"{$_applicant.user_name}" has applied to join your group "{$_group.group_title}".

Follow the link below to accept or reject this application -

{$_conf.jrCore_base_url}/{$murl}/user_config/group_id={$_group._item_id}/user_id={$_applicant._user_id}

You can view {$_applicant.user_name}'s profile here:

{$_conf.jrCore_base_url}/{$_applicant.profile_url}


