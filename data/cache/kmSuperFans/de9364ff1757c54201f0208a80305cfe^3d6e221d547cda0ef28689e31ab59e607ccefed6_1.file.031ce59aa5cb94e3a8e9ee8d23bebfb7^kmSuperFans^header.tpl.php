<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:09
  from "/webserver/mf6/data/cache/jrCore/031ce59aa5cb94e3a8e9ee8d23bebfb7^kmSuperFans^header.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b0731e82e82_92760056',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3d6e221d547cda0ef28689e31ab59e607ccefed6' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/031ce59aa5cb94e3a8e9ee8d23bebfb7^kmSuperFans^header.tpl',
      1 => 1528497969,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b0731e82e82_92760056 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"meta.tpl"),$_smarty_tpl) : '';?>

<body>

<div id="header">
    <div class="menu_pad">
        <div id="header_content" style="display: table; width: 100%;">
            <div style="display: table-row">
                <div style="width: 20%; height: 70px; display: table-cell; vertical-align: middle;">
                    <ul>
                        <li class="mobile" id="menu_button"><a href="#menu2"></a></li>
                        <li class="desk"><a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"logo.png",'width'=>"200",'height'=>"70",'class'=>"jlogo",'alt'=>$_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'],'custom'=>"logo"),$_smarty_tpl) : '';?>
</a></li>
                    </ul>
                </div>
                <div style="display: table-cell; vertical-align: middle;">
                    <?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"menu_main.tpl"),$_smarty_tpl) : '';?>

                </div>
            </div>
        </div>
    </div>
</div>

<?php echo (function_exists('smarty_function_jrCore_include')) ? smarty_function_jrCore_include(array('template'=>"menu_side.tpl"),$_smarty_tpl) : '';?>



<div id="searchform" class="search_box <?php echo $_smarty_tpl->tpl_vars['chameleon_style']->value;?>
" style="display:none;">
    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrSearch",'id'=>"1",'default'=>"Search Site",'assign'=>"st"),$_smarty_tpl) : '';?>

    <?php echo (function_exists('smarty_function_jrSearch_form')) ? smarty_function_jrSearch_form(array('class'=>"form_text",'value'=>$_smarty_tpl->tpl_vars['st']->value,'style'=>"width:70%"),$_smarty_tpl) : '';?>

    <div style="float:right;clear:both;margin-top:3px;">
        <a class="simplemodal-close"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"close",'size'=>20),$_smarty_tpl) : '';?>
</a>
    </div>
    <div class="clear"></div>
</div>

<div id="wrapper">

<?php if (strlen($_smarty_tpl->tpl_vars['page_template']->value) == 0) {?>
    <div id="content">
<?php }?>

<noscript>
    <div class="item error center" style="margin:12px">
        This site requires Javascript to function properly - please enable Javascript in your browser
    </div>
</noscript>

        <!-- end header.tpl -->
<?php }
}
