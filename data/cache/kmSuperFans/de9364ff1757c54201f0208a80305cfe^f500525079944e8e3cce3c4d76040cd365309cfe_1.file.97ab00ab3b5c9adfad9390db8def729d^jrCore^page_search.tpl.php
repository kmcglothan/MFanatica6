<?php
/* Smarty version 3.1.31, created on 2018-05-23 04:19:38
  from "/webserver/mf6/data/cache/jrCore/97ab00ab3b5c9adfad9390db8def729d^jrCore^page_search.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b04ddca585720_20527823',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f500525079944e8e3cce3c4d76040cd365309cfe' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/97ab00ab3b5c9adfad9390db8def729d^jrCore^page_search.tpl',
      1 => 1527045578,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b04ddca585720_20527823 (Smarty_Internal_Template $_smarty_tpl) {
?>
<tr>
  <td class="element_left search_area_left"><?php echo $_smarty_tpl->tpl_vars['label']->value;?>
</td>
  <td class="element_right search_area_right">
    <?php echo $_smarty_tpl->tpl_vars['html']->value;?>

    <?php if ($_smarty_tpl->tpl_vars['show_help']->value == '1') {?>
    <input type="button" value="?" class="form_button" title="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>34,'default'=>"expand help"),$_smarty_tpl) : '';?>
" onclick="$('#search_help').slideToggle(250);">
    <?php }?>
  </td>
</tr>
<?php if ($_smarty_tpl->tpl_vars['show_help']->value == '1') {?>
<tr>
  <td class="element_left form_input_left" style="padding:0;height:0"></td>
  <td>
    <div id="search_help" class="form_help" style="display:none;">

      <table class="form_help_drop">
        <tr>
          <td class="form_help_drop_left">
            Item Search Options:<br>
            <b>value</b> - Search for <b>exact</b> value match.<br>
            <b>%value</b> - Search for items that <b>end in</b> value.<br>
            <b>value%</b> - Search for items that <b>begin with</b> value.<br>
            <b>%value%</b> - Search for items that <b>contain</b> value.<br><br>
            Item Key Search Options:<br>
            <b>key:value</b> - Search for specific key with exact value match.<br>
            <b>key:%value</b> - Search for specific key that <b>begins with</b> value.<br>
            <b>key:value%</b> - Search for specific key that <b>ends with</b> value.<br>
            <b>key:%value%</b> - Search for specific key that <b>contains</b> value.
          </td>
        </tr>
      </table>

    </div>
  </td>
</tr>
<?php }
}
}
