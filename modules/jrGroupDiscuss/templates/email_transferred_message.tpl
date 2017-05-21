{$murl = jrCore_get_module_url($target_module)}
{$spfx = jrCore_db_get_prefix($source_module)}
{$tpfx = jrCore_db_get_prefix($target_module)}
Your {$source_type} "{$_source["`$spfx`_title"]}" has been transferred to {$target_type} "{$_target["`$tpfx`_title"]}"

See it here - <a href="{$target_url}">{$target_url}</a>
