<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:28
  from "/webserver/mf6/data/cache/jrCore/347602c624ab0997672fc1302981806c^jrAction^item_action_detail.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53ca0a958_20336758',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '94a3dbcf64b2e5a025d6977b104bd267a849f6b3' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/347602c624ab0997672fc1302981806c^jrAction^item_action_detail.tpl',
      1 => 1527493948,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53ca0a958_20336758 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="container">
    <div class="row">

        <div class="col2">
            <div class="action_item_media">
                <?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrUser",'type'=>"user_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_user_id'],'size'=>"icon",'crop'=>"auto",'alt'=>$_smarty_tpl->tpl_vars['item']->value['user_name'],'class'=>"action_item_user_img img_shadow img_scale"),$_smarty_tpl) : '';?>

            </div>
        </div>
        <div class="col10 last" style="position:relative">

            <div class="action_item_desc">

                <span class="action_item_title"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
" title="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['item']->value['profile_name']);?>
">@<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
</a> <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>42,'default'=>"posted"),$_smarty_tpl) : '';?>
</span>
                <span class="action_item_actions"> &bull; <?php echo smarty_modifier_jrCore_date_format($_smarty_tpl->tpl_vars['item']->value['_created'],"relative");?>
</span>

                <br>

                <div class="action_item_text">
                    <?php if (strlen($_smarty_tpl->tpl_vars['item']->value['action_text']) > 0) {?>
                    <?php echo $_smarty_tpl->tpl_vars['item']->value['action_text'];?>

                    <?php } else { ?>
                    <?php echo $_smarty_tpl->tpl_vars['item']->value['action_html'];?>

                    <?php }?>
                </div>

            </div>
        </div>

    </div>
</div>
<?php }
}
