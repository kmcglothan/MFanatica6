{jrCore_module_url module="jrGroup" assign="murl"}
Hello {$_member.user_name}

{$_commenter.user_name} has commented on the {$_conf.jrCore_system_name} group "{$_group.group_title}" -

{$_comment.comment_text}

To respond, visit {$_conf.jrCore_base_url}/{$_group.profile_url}/{$murl}/{$_group._item_id}/{$_group.group_title_url}
