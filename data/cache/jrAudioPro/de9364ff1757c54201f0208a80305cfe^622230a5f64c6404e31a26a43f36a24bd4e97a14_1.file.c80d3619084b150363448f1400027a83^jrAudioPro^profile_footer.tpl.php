<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:32
  from "/webserver/mf6/data/cache/jrCore/c80d3619084b150363448f1400027a83^jrAudioPro^profile_footer.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb5403aeaa6_93624222',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '622230a5f64c6404e31a26a43f36a24bd4e97a14' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c80d3619084b150363448f1400027a83^jrAudioPro^profile_footer.tpl',
      1 => 1527493952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb5403aeaa6_93624222 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div id="pm-drop-opt" style="display:none">
    <li class="hideshow"><a><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"jrAudioPro",'id'=>65,'default'=>"more"),$_smarty_tpl) : '';?>
 <span>&#x25BC;</span></a><ul id="submenu"></ul></li>
</div>

<?php echo (function_exists('smarty_function_jrCore_counter')) ? smarty_function_jrCore_counter(array('module'=>"jrProfile",'item_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'name'=>"profile_view"),$_smarty_tpl) : '';?>

</div>
</section>
<?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"footer.tpl"),$_smarty_tpl) : '';?>


<?php }
}
