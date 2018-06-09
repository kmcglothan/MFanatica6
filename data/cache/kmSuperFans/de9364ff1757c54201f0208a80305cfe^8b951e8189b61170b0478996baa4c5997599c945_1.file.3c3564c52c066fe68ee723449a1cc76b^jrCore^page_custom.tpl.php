<?php
/* Smarty version 3.1.31, created on 2018-05-24 23:01:25
  from "/webserver/mf6/data/cache/jrCore/3c3564c52c066fe68ee723449a1cc76b^jrCore^page_custom.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b07363599d7a1_11619577',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8b951e8189b61170b0478996baa4c5997599c945' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/3c3564c52c066fe68ee723449a1cc76b^jrCore^page_custom.tpl',
      1 => 1527199285,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b07363599d7a1_11619577 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['label']->value) && strlen($_smarty_tpl->tpl_vars['label']->value) > 0) {?>
    <tr>
        <td class="element_left form_input_left <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_left">
            <?php echo $_smarty_tpl->tpl_vars['label']->value;
if (isset($_smarty_tpl->tpl_vars['sublabel']->value) && strlen($_smarty_tpl->tpl_vars['sublabel']->value) > 0) {?><br><span class="sublabel"><?php echo $_smarty_tpl->tpl_vars['sublabel']->value;?>
</span><?php }?>
        </td>
        <td class="element_right form_input_right <?php echo $_smarty_tpl->tpl_vars['type']->value;?>
_right" style="position:relative">
            <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

        <?php if (isset($_smarty_tpl->tpl_vars['help']->value) && strlen($_smarty_tpl->tpl_vars['help']->value) > 0) {?>
            <input type="button" value="?" class="form_button form_help_button" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>34,'default'=>"expand help"),$_smarty_tpl) : '';?>
" onclick="$('#h_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
').slideToggle(250);">
        <?php }?>
        </td>
    </tr>
<?php } else { ?>
    <tr>
        <td colspan="2" class="element page_custom"><?php echo $_smarty_tpl->tpl_vars['html']->value;?>
</td>
    </tr>
<?php }
if (isset($_smarty_tpl->tpl_vars['help']->value) && strlen($_smarty_tpl->tpl_vars['help']->value) > 0) {?>
    <tr>
        <td class="element_left form_input_left" style="padding:0;height:0"></td>
        <td>
            <div id="h_<?php echo $_smarty_tpl->tpl_vars['name']->value;?>
" class="form_help" style="display:none">

                <table class="form_help_drop">
                    <tr>
                        <td class="form_help_drop_left">
                            <?php echo $_smarty_tpl->tpl_vars['help']->value;?>

                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>
<?php }
}
}
