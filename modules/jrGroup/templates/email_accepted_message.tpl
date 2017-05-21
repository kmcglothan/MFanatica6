{jrCore_module_url module="jrGroup" assign="murl"}
Hello {$_applicant.user_name} -

Your "{$_group.group_title}" Group Application has been accepted!

Follow the link below to visit the group and interact with fellow members:

{$_conf.jrCore_base_url}/{$_group.profile_url}/{$murl}/{$_group._item_id}/{$_group.group_title_url}
