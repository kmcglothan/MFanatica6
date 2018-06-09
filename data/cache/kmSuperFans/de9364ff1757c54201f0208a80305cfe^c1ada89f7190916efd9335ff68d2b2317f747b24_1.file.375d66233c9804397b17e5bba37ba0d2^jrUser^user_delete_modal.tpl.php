<?php
/* Smarty version 3.1.31, created on 2018-05-22 05:45:20
  from "/webserver/mf6/data/cache/jrCore/375d66233c9804397b17e5bba37ba0d2^jrUser^user_delete_modal.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b03a0600f9348_08457473',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c1ada89f7190916efd9335ff68d2b2317f747b24' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/375d66233c9804397b17e5bba37ba0d2^jrUser^user_delete_modal.tpl',
      1 => 1526964320,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b03a0600f9348_08457473 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="modal_window" class="user-delete-modal" style="display:none">
    <input type="button" id="d-user" class="form_button" name="d-user" value="delete user account only" onclick="jrUser_delete_user_from_modal()">
    <input type="button" id="d-profile" class="form_button" name="d-profile" value="delete user account and profile" onclick="jrUser_delete_profile_from_modal()">
    <input type="button" id="d-cancel" class="form_button" name="d-cancel" value="cancel" onclick="$.modal.close()">
    <div id="user-delete-active-user-id" style="display:none">0</div>
    <div id="user-delete-active-profile-id" style="display:none">0</div>
</div>
<?php }
}
