<?php
/* Smarty version 3.1.31, created on 2018-05-17 19:00:26
  from "/webserver/mf6/data/cache/jrCore/456f537b57c4ceeec38358ad01cf8270^jrSearch^search_results.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5afdc33a3ea032_56851956',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6036a9e9910885d82c1fed0ef2a0113a43abcebf' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/456f537b57c4ceeec38358ad01cf8270^jrSearch^search_results.tpl',
      1 => 1526580026,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5afdc33a3ea032_56851956 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_replace')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.replace.php';
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrSearch",'assign'=>"murl"),$_smarty_tpl) : '';?>


<div class="container search_results_container">
    <div class="row">
        <div class="col12 last">
            <div class="title">

                <?php if ($_smarty_tpl->tpl_vars['module_count']->value == 1) {?>
                    <h1>&quot;<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['search_string']->value,"&quot;",'');?>
&quot; in <?php echo $_smarty_tpl->tpl_vars['titles']->value[$_smarty_tpl->tpl_vars['modules']->value];?>
</h1>
                    <div class="breadcrumbs">
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"11",'default'=>"Home"),$_smarty_tpl) : '';?>
</a> &raquo;
                        <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/results/all/1/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_conf']->value['jrSearch_index_limit'])===null||strlen($tmp)===0||$tmp==='' ? 4 : $tmp);?>
/search_string=<?php echo $_smarty_tpl->tpl_vars['search_string']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"6",'default'=>"All Search Results for"),$_smarty_tpl) : '';?>
 &quot;<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['search_string']->value,'&quot;','');?>
&quot;</a> &raquo; <?php echo $_smarty_tpl->tpl_vars['titles']->value[$_smarty_tpl->tpl_vars['modules']->value];?>

                    </div>
                <?php } else { ?>
                    <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"8",'default'=>"Search Results for"),$_smarty_tpl) : '';?>
 &quot;<?php echo smarty_modifier_replace($_smarty_tpl->tpl_vars['search_string']->value,"&quot;",'');?>
&quot;</h1>
                <?php }?>

            </div>

            <div class="block" style="margin-bottom:12px">
                <div class="block_content">
                    <form id="sr" method="get" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/results/<?php echo $_smarty_tpl->tpl_vars['modules']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['pagebreak']->value;?>
" target="_self">
                        <input type="text" name="search_string" class="form_text" value="<?php echo $_smarty_tpl->tpl_vars['search_string']->value;?>
">
                        <br><span style="display:inline-block;margin-top:8px;"><img id="form_submit_indicator" src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/img/submit.gif" width="24" height="24" alt="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrCore",'id'=>"73",'default'=>"working..."),$_smarty_tpl) : '';?>
"><input type="button" class="form_button" value="<?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"7",'default'=>"search"),$_smarty_tpl) : '';?>
 <?php echo $_smarty_tpl->tpl_vars['titles']->value[$_smarty_tpl->tpl_vars['modules']->value];?>
" onclick="jrSearch_refine_results()"></span>
                    </form>
                </div>

            </div>
        </div>
    </div>


    <?php if (count($_smarty_tpl->tpl_vars['results']->value) > 0) {?>

        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['results']->value, 'result', true, 'module');
$_smarty_tpl->tpl_vars['result']->iteration = 0;
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['module']->value => $_smarty_tpl->tpl_vars['result']->value) {
$_smarty_tpl->tpl_vars['result']->iteration++;
$_smarty_tpl->tpl_vars['result']->last = $_smarty_tpl->tpl_vars['result']->iteration == $_smarty_tpl->tpl_vars['result']->total;
$__foreach_result_12_saved = $_smarty_tpl->tpl_vars['result'];
?>

        <?php if ($_smarty_tpl->tpl_vars['module_count']->value > 1) {?>
            <?php if ($_smarty_tpl->tpl_vars['result']->iteration%2 === 1) {?>
            <div class="row">
                <div class="col6">
                    <div style="margin:6px 6px 6px 0">
            <?php } else { ?>
            <div class="col6 last">
                <div style="margin:6px 0 6px 6px">
            <?php }?>
        <?php } else { ?>
            <div class="row">
                <div class="col12 last">
                    <div>
        <?php }?>

                        <div class="title">
                            <div class="block_config">
                            <?php if ($_smarty_tpl->tpl_vars['module_count']->value > 1 && $_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['total_items'] > $_smarty_tpl->tpl_vars['pagebreak']->value) {?>
                                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/results/<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['page']->value;?>
/<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_conf']->value['jrSearch_result_limit'])===null||strlen($tmp)===0||$tmp==='' ? 12 : $tmp);?>
/search_string=<?php echo $_smarty_tpl->tpl_vars['search_string']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"9",'default'=>"See All Results"),$_smarty_tpl) : '';?>
 (<?php echo jrCore_number_format($_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['total_items']);?>
) &raquo; </a>
                            <?php }?>
                            </div>
                            <h1><?php echo $_smarty_tpl->tpl_vars['titles']->value[$_smarty_tpl->tpl_vars['module']->value];?>
</h1>
                        </div>

                        <div class="block_content"><?php echo $_smarty_tpl->tpl_vars['result']->value;?>
</div>

                   </div>
              </div>

            <?php if ($_smarty_tpl->tpl_vars['result']->iteration%2 === 0 || $_smarty_tpl->tpl_vars['module_count']->value == '1' || $_smarty_tpl->tpl_vars['result']->last === true) {?>
            </div>
            <?php }?>

        <?php
$_smarty_tpl->tpl_vars['result'] = $__foreach_result_12_saved;
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


        
        <?php if ($_smarty_tpl->tpl_vars['module_count']->value == 1) {?>

            <?php if ($_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['prev_page'] > 0 || $_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['next_page'] > 0) {?>
                <div class="block">
                    <table style="width:100%">
                        <tr>
                            <td style="width:25%">
                                <?php if ($_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['prev_page'] > 0) {?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/results/<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['prev_page'];?>
/<?php echo $_smarty_tpl->tpl_vars['pagebreak']->value;?>
/search_string=<?php echo $_smarty_tpl->tpl_vars['search_string']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"previous"),$_smarty_tpl) : '';?>
</a>
                                <?php }?>
                            </td>
                            <td style="width:50%;text-align:center">
                                <?php echo $_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['this_page'];?>

                            </td>
                            <td style="width:25%;text-align:right">
                                <?php if ($_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['next_page'] > 0) {?>
                                    <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/results/<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['info']->value[$_smarty_tpl->tpl_vars['module']->value]['next_page'];?>
/<?php echo $_smarty_tpl->tpl_vars['pagebreak']->value;?>
/search_string=<?php echo $_smarty_tpl->tpl_vars['search_string']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"next"),$_smarty_tpl) : '';?>
</a>
                                <?php }?>
                            </td>
                        </tr>
                    </table>
                </div>
            <?php }?>
        <?php }?>

    <?php } else { ?>

        <div class="row">
            <div class="col12 last">
                <div class="page_note" style="margin-bottom:12px">
                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"10",'default'=>"No results found for your search"),$_smarty_tpl) : '';?>

                </div>
            </div>
        </div>

    <?php }?>

</div>

<?php }
}
