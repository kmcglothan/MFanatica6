<?php
/* Smarty version 3.1.31, created on 2018-05-17 19:00:25
  from "/webserver/mf6/data/cache/jrCore/abb8108159c5e9868b4918ffd0d833f1^jrProfile^item_search.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5afdc339f39cd6_65657069',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '29b1bb785f6b6a302fffc7294410e9805c4a47df' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/abb8108159c5e9868b4918ffd0d833f1^jrProfile^item_search.tpl',
      1 => 1526580025,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5afdc339f39cd6_65657069 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_truncate')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.truncate.php';
if (isset($_smarty_tpl->tpl_vars['_items']->value)) {?>

    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
    <div class="item">

        <div class="container">
            <div class="row">
                <div class="col2">
                    <div class="block_image">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo (function_exists('smarty_function_jrCore_module_function')) ? smarty_function_jrCore_module_function(array('function'=>"jrImage_display",'module'=>"jrProfile",'type'=>"profile_image",'item_id'=>$_smarty_tpl->tpl_vars['item']->value['_profile_id'],'size'=>"large",'crop'=>"auto",'class'=>"iloutline img_scale",'alt'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'title'=>$_smarty_tpl->tpl_vars['item']->value['profile_name'],'width'=>false,'height'=>false),$_smarty_tpl) : '';?>
</a>
                    </div>
                </div>
                <div class="col10 last">
                    <div style="padding:0 6px 0 12px;overflow-wrap:break-word">
                        <h2><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['item']->value['profile_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['item']->value['profile_name'];?>
</a></h2>
                        <?php if (!empty($_smarty_tpl->tpl_vars['item']->value['profile_bio'])) {?>
                        <br><?php echo smarty_modifier_truncate(smarty_modifier_jrCore_strip_html(smarty_modifier_jrCore_format_string($_smarty_tpl->tpl_vars['item']->value['profile_bio'],$_smarty_tpl->tpl_vars['item']->value['profile_quota_id'])),180,"...");?>

                        <?php }?>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
}
}
