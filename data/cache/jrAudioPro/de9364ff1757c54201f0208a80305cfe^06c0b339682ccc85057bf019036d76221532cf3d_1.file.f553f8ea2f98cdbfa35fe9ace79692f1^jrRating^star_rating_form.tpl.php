<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/f553f8ea2f98cdbfa35fe9ace79692f1^jrRating^star_rating_form.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f89fcbd9_39424419',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '06c0b339682ccc85057bf019036d76221532cf3d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/f553f8ea2f98cdbfa35fe9ace79692f1^jrRating^star_rating_form.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f89fcbd9_39424419 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div style="display:inline-block;vertical-align:middle;">
    <ul class="star-rating">
        <li id="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
" class="current-rating" style="width:<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['current'];?>
%;"></li>
        <li><a title="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['values'][1];?>
" class="star1-rating" onclick="jrRating_rate_item('#<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
','1','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['module_url'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['item_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['index'];?>
');"></a></li>
        <li><a title="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['values'][2];?>
" class="star2-rating" onclick="jrRating_rate_item('#<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
','2','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['module_url'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['item_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['index'];?>
');"></a></li>
        <li><a title="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['values'][3];?>
" class="star3-rating" onclick="jrRating_rate_item('#<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
','3','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['module_url'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['item_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['index'];?>
');"></a></li>
        <li><a title="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['values'][4];?>
" class="star4-rating" onclick="jrRating_rate_item('#<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
','4','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['module_url'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['item_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['index'];?>
');"></a></li>
        <li><a title="<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['values'][5];?>
" class="star5-rating" onclick="jrRating_rate_item('#<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['html_id'];?>
','5','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['module_url'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['item_id'];?>
','<?php echo $_smarty_tpl->tpl_vars['jrRating']->value['index'];?>
');"></a></li>
    </ul>
</div>

<?php }
}
