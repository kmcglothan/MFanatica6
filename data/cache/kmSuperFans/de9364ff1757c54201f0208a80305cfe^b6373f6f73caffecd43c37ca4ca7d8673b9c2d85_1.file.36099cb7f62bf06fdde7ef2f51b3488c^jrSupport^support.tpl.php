<?php
/* Smarty version 3.1.31, created on 2018-05-17 20:16:45
  from "/webserver/mf6/data/cache/jrCore/36099cb7f62bf06fdde7ef2f51b3488c^jrSupport^support.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5afdd51d4a9ac0_18779737',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b6373f6f73caffecd43c37ca4ca7d8673b9c2d85' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/36099cb7f62bf06fdde7ef2f51b3488c^jrSupport^support.tpl',
      1 => 1526584605,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5afdd51d4a9ac0_18779737 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div style="overflow: hidden">
<?php if (isset($_smarty_tpl->tpl_vars['module']->value)) {?>
    <?php echo '<script'; ?>
>
        $(document).ready(function() {
            jrSupport_view_options('module', '<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
');
        });
    <?php echo '</script'; ?>
>
    <div id="info_box">
        <div class="item" style="display:table;width:100%;margin:0">
            <div style="display:table-row">
                <div style="display:table-cell;width:10%;text-align:center">
                    <?php echo $_smarty_tpl->tpl_vars['icon']->value;?>

                </div>
                <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                    <h2>Module Questions</h2>
                    <ul>
                        <li>Have a question about how a module works?</li>
                        <li>Need help configuring a module to suit your needs?</li>
                        <li>Encountered an issue with a module and need help?</li>
                    </ul>
                    <img id="module_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/form_spinner.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl) : '';?>
" style="display:none;margin:2px 0 7px 6px">
                </div>
            </div>
        </div>
    </div>
    <img id="module_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/form_spinner.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl) : '';?>
" style="display:none;margin:2px 0 7px 6px">
    <div id="module_info" style="display:none"></div>

<?php } else { ?>

    <?php echo '<script'; ?>
>
        $(document).ready(function() {
            jrSupport_view_options('skin', '<?php echo $_smarty_tpl->tpl_vars['skin']->value;?>
');
        });
    <?php echo '</script'; ?>
>
    <div id="info_box">
        <div class="item" style="display:table;width:100%;margin:0">
            <div style="display:table-row">
                <div style="display:table-cell;width:10%;text-align:center">
                    <?php echo $_smarty_tpl->tpl_vars['icon']->value;?>

                </div>
                <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                    <h2>Skin Questions and Customization</h2>
                    <ul>
                        <li>Need help designing or customizing the skin templates?</li>
                        <li>Have questions about a skin configuration?</li>
                        <li>Encountered an issue and need help?</li>
                    </ul>
                    <img id="skin_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/form_spinner.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl) : '';?>
" style="display:none;margin:2px 0 7px 6px">
                </div>
            </div>
        </div>
    </div>
    <div id="skin_info"></div>
<?php }?>
</div>
<?php }
}
