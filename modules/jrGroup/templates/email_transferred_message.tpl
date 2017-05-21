{$pfx = jrCore_db_get_prefix($module)}
{$murl = jrCore_get_module_url($module)}
Your {$type} "{$_source["`$pfx`_title"]}" has been transferred to group "{$_target['group_title']}"

See it here - <a href="{$_conf['jrCore_base_url']}/{$_target['profile_url']}/{$murl}/{$_source['_item_id']}/{$_source["`$pfx`_title_url"]}">{$_conf['jrCore_base_url']}/{$_target['profile_url']}/{$murl}/{$_source['_item_id']}/{$_source["`$pfx`_title_url"]}</a>
