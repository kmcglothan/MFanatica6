<?php
/* Smarty version 3.1.31, created on 2018-05-17 20:16:54
  from "/webserver/mf6/data/cache/jrCore/fffad23051059c131ddaeeaa680bee1c^jrSupport^options.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5afdd5268414b0_56158147',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ac4986ea1629542679cdbccf0f8a37b76ea625cf' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/fffad23051059c131ddaeeaa680bee1c^jrSupport^options.tpl',
      1 => 1526584614,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5afdd5268414b0_56158147 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="item">

    <?php if (isset($_smarty_tpl->tpl_vars['market_title']->value)) {?>
    <div id="jrsupport-options" style="display:table;width:100%">

        <div style="display:table-row">
            <div class="p10" style="display:table-cell;width:100%">
                <h2><?php echo $_smarty_tpl->tpl_vars['market_title']->value;?>
</h2><br>by <a href="<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
" target="_blank">@<?php echo basename($_smarty_tpl->tpl_vars['profile_url']->value);?>
</a>
            </div>
        </div>

        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="<?php echo $_smarty_tpl->tpl_vars['documentation_url']->value;?>
" target="_blank"><input type="button" value="Documentation" class="form_button form_button_support"></a> &nbsp; View the online documentation for this item
            </div>
        </div>

        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="<?php echo $_smarty_tpl->tpl_vars['forum_url']->value;?>
" target="_blank"><input type="button" value="Community Forum" class="form_button form_button_support"></a> &nbsp; Check for Help and Answers in the Community Forum
            </div>
        </div>

        <?php if (isset($_smarty_tpl->tpl_vars['priority_url']->value)) {?>
        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="<?php echo $_smarty_tpl->tpl_vars['priority_url']->value;?>
" target="_blank"><input type="button" value="Support Ticket" class="form_button form_button_support"></a> &nbsp; Open a Support Ticket to get help with your questions
            </div>
        </div>
        <?php }?>

        <?php if (isset($_smarty_tpl->tpl_vars['market_url']->value)) {?>
        <div style="display:table-row">
            <div class="jrsupport-entry">
                <a href="<?php echo $_smarty_tpl->tpl_vars['market_url']->value;?>
" target="_blank"><input type="button" value="Product Detail" class="form_button form_button_support"></a> &nbsp; View information about this item including detailed Change Log
            </div>
        </div>
        <?php }?>

    </div>

    <?php } else { ?>

    <div class="center">
        <div class="p10 error rounded">
        <?php if (isset($_smarty_tpl->tpl_vars['error']->value)) {?>
            <?php echo $_smarty_tpl->tpl_vars['error']->value;?>

        <?php } else { ?>
            No support information available for the selected item
        <?php }?>
        </div>
    </div>

    <?php }?>

</div>
<?php }
}
