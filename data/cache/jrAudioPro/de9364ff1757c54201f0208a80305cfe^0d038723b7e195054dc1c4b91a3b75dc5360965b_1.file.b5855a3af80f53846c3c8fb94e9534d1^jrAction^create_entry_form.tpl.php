<?php
/* Smarty version 3.1.31, created on 2018-05-26 08:19:02
  from "/webserver/mf6/data/cache/jrCore/b5855a3af80f53846c3c8fb94e9534d1^jrAction^create_entry_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b090a6630d7d2_14404176',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0d038723b7e195054dc1c4b91a3b75dc5360965b' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/b5855a3af80f53846c3c8fb94e9534d1^jrAction^create_entry_form.tpl',
      1 => 1527319142,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b090a6630d7d2_14404176 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"iurl"),$_smarty_tpl) : '';?>


<?php if (strlen($_smarty_tpl->tpl_vars['editor_html']->value) > 10) {?>

    <style type="text/css">.form_editor_holder { width: 100% !important }</style>

<?php } else { ?>

    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrAction/contrib/mentions/jquery.mentionsInput.css?v=<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
" type="text/css">
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrAction/contrib/underscore/underscore-min.js?v=<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrAction/contrib/mentions/jquery.mentionsInput.js?v=<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrAction/contrib/mentions/lib/jquery.elastic.js?v=<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/modules/jrAction/contrib/mentions/lib/jquery.events.input.js?v=<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
 type="text/javascript">
        $(document).ready(function()
        {
            $('#action_update').mentionsInput({
                onDataRequest: function(mode, query, callback)
                {
                    var d = 'q=' + query;
                    $.getJSON('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/mention_profiles', d, function(r)
                    {
                        r = _.filter(r, function(i)
                        {
                            return i.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                        });
                        callback.call(this, r);
                    });
                }
            });
        });
    <?php echo '</script'; ?>
>

<?php }?>

<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>3,'default'=>"Post a new Activity Update",'assign'=>"ph"),$_smarty_tpl) : '';?>

<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>5,'default'=>"save update",'assign'=>"su"),$_smarty_tpl) : '';?>


<div id="quick_action_box">

    <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrAction_quick_share'] == 'on' && count($_smarty_tpl->tpl_vars['_tabs']->value) > 1 && $_smarty_tpl->tpl_vars['quick_share']->value == 1) {?>
    <div id="quick_action_tab_box">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_tabs']->value, '_t');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_t']->value) {
?>
            <?php if ($_smarty_tpl->tpl_vars['_t']->value['module'] == 'jrAction') {?>
                <div class="quick_action_tab quick_action_tab_active" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['_t']->value['title']);?>
" onclick="jrAction_quick_share(this, '<?php echo $_smarty_tpl->tpl_vars['_t']->value['module'];?>
','<?php echo $_smarty_tpl->tpl_vars['_t']->value['function'];?>
')"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>$_smarty_tpl->tpl_vars['_t']->value['icon'],'size'=>30,'class'=>"sprite_icon_hilighted"),$_smarty_tpl) : '';?>
</div>
            <?php } else { ?>
                <div class="quick_action_tab" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['_t']->value['title']);?>
" onclick="jrAction_quick_share(this, '<?php echo $_smarty_tpl->tpl_vars['_t']->value['module'];?>
','<?php echo $_smarty_tpl->tpl_vars['_t']->value['function'];?>
')"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>$_smarty_tpl->tpl_vars['_t']->value['icon'],'size'=>30),$_smarty_tpl) : '';?>
</div>
            <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        <div id="quick_action_title"><?php echo $_smarty_tpl->tpl_vars['ph']->value;?>
</div>
    </div>
    <div style="clear:both"></div>
    <?php }?>

    <form id="action_form" method="post" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/create_save" onsubmit="jrAction_submit();return false">
        <input type="hidden" name="jr_html_form_token" value="<?php echo $_smarty_tpl->tpl_vars['token']->value;?>
">
        <input id="jrAction_function" type="hidden" name="jrAction_function" value="jrAction_quick_share_status_update">

        <div id="quick_action_form">
            
        </div>

        <div id="quick_action_default_form">
        <?php if (strlen($_smarty_tpl->tpl_vars['editor_html']->value) > 10) {?>

            <?php echo $_smarty_tpl->tpl_vars['editor_html']->value;?>


        <?php } else { ?>

            <textarea cols="72" rows="6" id="action_update" class="form_textarea" name="action_text" placeholder="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['ph']->value);?>
"></textarea>

        <?php }?>
        </div>

        <img id="asi" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['iurl']->value;?>
/img/skin/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/form_spinner.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>73,'default'=>"working..."),$_smarty_tpl) : '';?>
" style="display:none">
        <input id="action_submit" type="button" class="form_button" value="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['su']->value);?>
" onclick="$('#action_form').submit();">

    </form>
</div>
<?php }
}
