<?php
/* Smarty version 3.1.31, created on 2018-05-26 08:18:42
  from "/webserver/mf6/data/cache/jrCore/aa37b849a5156a63fdcf829bab534fa4^jrCore^form_submit.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b090a52ace336_09048151',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a2f806668e8233bee933e6c3b6491742cc7a9648' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/aa37b849a5156a63fdcf829bab534fa4^jrCore^form_submit.tpl',
      1 => 1527319122,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b090a52ace336_09048151 (Smarty_Internal_Template $_smarty_tpl) {
?>

<tr>
  <td colspan="2" class="form_submit_box">
    <div class="form_submit_section">
        <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"url"),$_smarty_tpl) : '';?>

        <img id="form_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/img/skin/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/submit.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl) : '';?>
"><?php echo $_smarty_tpl->tpl_vars['html']->value;?>

    </div>
  </td>
</tr>
<?php }
}
