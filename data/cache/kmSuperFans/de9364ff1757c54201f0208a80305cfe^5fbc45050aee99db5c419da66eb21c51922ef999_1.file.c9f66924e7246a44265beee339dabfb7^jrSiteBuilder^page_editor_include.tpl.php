<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:00:54
  from "/webserver/mf6/data/cache/jrCore/c9f66924e7246a44265beee339dabfb7^jrSiteBuilder^page_editor_include.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b073616155a98_32790662',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5fbc45050aee99db5c419da66eb21c51922ef999' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c9f66924e7246a44265beee339dabfb7^jrSiteBuilder^page_editor_include.tpl',
      1 => 1527199254,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b073616155a98_32790662 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['_conf']->value['jrSiteBuilder_enabled'] == 'on') {?>
<div id="sb-include-section">

    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/tinymce/tinymce.min.js?v=<?php echo $_smarty_tpl->tpl_vars['_mods']->value['jrCore']['module_version'];?>
"><?php echo '</script'; ?>
>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/codemirror/lib/codemirror.css" media="screen" />
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/codemirror/lib/codemirror.js?v=<?php echo $_smarty_tpl->tpl_vars['_mods']->value['jrCore']['module_version'];?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js?v=<?php echo $_smarty_tpl->tpl_vars['_mods']->value['jrCore']['module_version'];?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrSiteBuilder/js/jquery.nouislider.min.js?v=<?php echo $_smarty_tpl->tpl_vars['_mods']->value['jrSiteBuilder']['module_version'];?>
"><?php echo '</script'; ?>
>
    <?php echo (function_exists('smarty_function_jrSiteBuilder_tinymce_init')) ? smarty_function_jrSiteBuilder_tinymce_init(array(),$_smarty_tpl) : '';?>

    <?php echo '<script'; ?>
 type="text/javascript">
        var cm; //for codemirror editor.
    <?php echo '</script'; ?>
>


    <div id="sb-layout-section">
        <div id="sb-doc-menu" class="sb-button" onclick="window.open('https://www.jamroom.net/r/site-builder-help');">Help</div>
        <div id="sb-edit-menu" class="sb-button" onclick="jrSiteBuilder_edit_menu()">Menu Editor</div>
        <div id="sb-page-delete" class="sb-button" onclick="jrCore_confirm('Delete This Page?', 'Are you sure you want to delete this page?',function() { jrSiteBuilder_delete_page('<?php echo $_smarty_tpl->tpl_vars['page_id']->value;?>
') } )">Delete Page</div>
        <div id="sb-edit-layout" class="sb-button" onclick="jrSiteBuilder_edit_layout('<?php echo $_smarty_tpl->tpl_vars['page_id']->value;?>
')">Page Config</div>
        <div id="sb-close-button" class="sb-button" onclick="jrSiteBuilder_close()">Close</div>
    </div>

    <?php if (isset($_smarty_tpl->tpl_vars['notice']->value)) {?>
        <div id="sb-edit-menu" class="sb-button" style="bottom: 76px" onclick="jrSiteBuilder_edit_menu()">Menu Editor</div>
        <div id="sb-edit-button" class="sb-button" onclick="jrCore_confirm('Change This Page?', '<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['notice']->value);?>
',function() { jrSiteBuilder_create_and_edit_page() } )">Site Builder</div>
    <?php } else { ?>
        <div id="sb-edit-button" class="sb-button" onclick="jrSiteBuilder_edit_page('<?php echo $_smarty_tpl->tpl_vars['page_id']->value;?>
')">Site Builder</div>
    <?php }?>

    <div id="sb-edit-cp-holder">
        <div id="sb-edit-cp" class="block_content">
           
        </div>
    </div>

    
    <link rel="stylesheet" property="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/core/icon_css/20?_v=<?php echo time();?>
" />

</div>
<?php }
}
}
