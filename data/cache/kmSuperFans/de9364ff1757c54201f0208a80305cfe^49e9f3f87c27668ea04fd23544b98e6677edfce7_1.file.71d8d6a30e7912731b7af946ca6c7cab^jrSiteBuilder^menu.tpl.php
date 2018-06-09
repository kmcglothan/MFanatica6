<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/71d8d6a30e7912731b7af946ca6c7cab^jrSiteBuilder^menu.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b0732702738_85154134',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '49e9f3f87c27668ea04fd23544b98e6677edfce7' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/71d8d6a30e7912731b7af946ca6c7cab^jrSiteBuilder^menu.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b0732702738_85154134 (Smarty_Internal_Template $_smarty_tpl) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_list']->value, '_l0');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_l0']->value) {
?>
<li <?php if ($_smarty_tpl->tpl_vars['_post']->value['module_url'] == $_smarty_tpl->tpl_vars['_l0']->value['menu_url']) {?>class="active"<?php }?>>
    <a href="<?php if (substr($_smarty_tpl->tpl_vars['_l0']->value['menu_url'],0,4) === 'http') {
echo $_smarty_tpl->tpl_vars['_l0']->value['menu_url'];
} elseif (substr($_smarty_tpl->tpl_vars['_l0']->value['menu_url'],0,1) === '#') {
echo $_smarty_tpl->tpl_vars['_l0']->value['menu_url'];
} else {
echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_l0']->value['menu_url'];
}?>" onclick="<?php echo $_smarty_tpl->tpl_vars['_l0']->value['menu_onclick'];?>
" class="menu_0_link" data-topic="<?php echo $_smarty_tpl->tpl_vars['_l0']->value['menu_url'];?>
"><?php echo $_smarty_tpl->tpl_vars['_l0']->value['menu_title'];?>
</a>
    <?php if (is_array($_smarty_tpl->tpl_vars['_l0']->value['_children'])) {?>
    <ul>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_l0']->value['_children'], '_l1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_l1']->value) {
?>
        <li>
            <a href="<?php if (substr($_smarty_tpl->tpl_vars['_l1']->value['menu_url'],0,4) === 'http') {
echo $_smarty_tpl->tpl_vars['_l1']->value['menu_url'];
} elseif (substr($_smarty_tpl->tpl_vars['_l1']->value['menu_url'],0,1) === '#') {
echo $_smarty_tpl->tpl_vars['_l1']->value['menu_url'];
} else {
echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_l1']->value['menu_url'];
}?>" onclick="<?php echo $_smarty_tpl->tpl_vars['_l1']->value['menu_onclick'];?>
" ><?php echo $_smarty_tpl->tpl_vars['_l1']->value['menu_title'];?>
</a>
            <?php if (is_array($_smarty_tpl->tpl_vars['_l1']->value['_children'])) {?>
            <ul>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['_l1']->value['_children'], '_l2');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_l2']->value) {
?>
                <li><a href="<?php if (substr($_smarty_tpl->tpl_vars['_l2']->value['menu_url'],0,4) === 'http') {
echo $_smarty_tpl->tpl_vars['_l2']->value['menu_url'];
} elseif (substr($_smarty_tpl->tpl_vars['_l2']->value['menu_url'],0,1) === '#') {
echo $_smarty_tpl->tpl_vars['_l2']->value['menu_url'];
} else {
echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['_l2']->value['menu_url'];
}?>" onclick="<?php echo $_smarty_tpl->tpl_vars['_l2']->value['menu_onclick'];?>
" ><?php echo $_smarty_tpl->tpl_vars['_l2']->value['menu_title'];?>
</a></li>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </ul>
            <?php }?>
        </li>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </ul>
    <?php }?>
</li>
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
