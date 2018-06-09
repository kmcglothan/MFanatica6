<?php
/* Smarty version 3.1.31, created on 2018-05-18 08:46:53
  from "/webserver/mf6/data/cache/jrCore/864b1ba388386c376912b2428420b110^jrSiteBuilder^page_create.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5afe84ed0d2063_97337060',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5ee4db3b8739786280ec5dff75ab505b24178c10' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/864b1ba388386c376912b2428420b110^jrSiteBuilder^page_create.tpl',
      1 => 1526629612,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5afe84ed0d2063_97337060 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_page_title')) ? smarty_function_jrCore_page_title(array('title'=>"Create new Page?"),$_smarty_tpl) : '';?>


<?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"header.tpl"),$_smarty_tpl) : '';?>


<div class="container">

    <div class="row">
        <div class="col12 last">
            <div class="block center">
                <div class="title" style="padding:30px;">

                    <h1>This page does not exist</h1>
                    <br><br>
                    <?php if (strlen($_smarty_tpl->tpl_vars['create_notice']->value) > 0) {?>
                        <?php echo $_smarty_tpl->tpl_vars['create_notice']->value;?>

                    <?php }?>
                    Would you like to create this page in Site Builder?
                    <br><br>
                    <input type="button" class="form_button" value="Yes - Create This Page" onclick="jrSiteBuilder_create_and_edit_page()">

                </div>
            </div>
        </div>
    </div>

</div>

<?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"footer.tpl"),$_smarty_tpl) : '';?>

<?php }
}
