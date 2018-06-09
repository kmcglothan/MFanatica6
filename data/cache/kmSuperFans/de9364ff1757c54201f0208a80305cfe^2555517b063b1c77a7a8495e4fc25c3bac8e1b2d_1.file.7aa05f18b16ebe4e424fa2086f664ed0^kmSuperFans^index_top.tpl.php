<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/7aa05f18b16ebe4e424fa2086f664ed0^kmSuperFans^index_top.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b07327d50f2_59821830',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2555517b063b1c77a7a8495e4fc25c3bac8e1b2d' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/7aa05f18b16ebe4e424fa2086f664ed0^kmSuperFans^index_top.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b07327d50f2_59821830 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="index_top">
    <div class="row">
        <div class="col12">
            <div class="index_slide">

                <?php if (jrCore_is_mobile_device()) {?>
                    <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"index_top_mobile.jpg",'width'=>"800",'height'=>"auto"),$_smarty_tpl) : '';?>

                <?php } else { ?>
                    <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('image'=>"index_top.jpg",'width'=>"1280",'height'=>"auto"),$_smarty_tpl) : '';?>

                <?php }?>

                <div class="slide_info">
                    <div class="wrap">
                        <ul class="social clearfix">
                            <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_facebook_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_facebook_url'] != "0") {?>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_facebook_url'];?>
" class="social-facebook" target="_blank"></a></li>
                            <?php }?>
                            <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_twitter_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_twitter_url'] != "0") {?>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_twitter_url'];?>
" class="social-twitter" target="_blank"></a></li>
                            <?php }?>
                            <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_google_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_google_url'] != "0") {?>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_google_url'];?>
" class="social-google" target="_blank"></a></li>
                            <?php }?>
                            <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_linkedin_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_linkedin_url'] != "0") {?>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_linkedin_url'];?>
" class="social-linkedin" target="_blank"></a></li>
                            <?php }?>
                            <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_youtube_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_youtube_url'] != "0") {?>
                                <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_youtube_url'];?>
" class="social-youtube" target="_blank"></a></li>
                            <?php }?>
                        </ul>
                        <span class="large white"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>51,'default'=>"Welcome to"),$_smarty_tpl) : '';?>
 </span> <span class="large"><?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</span><br>
                        <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>79,'default'=>"Member of the "),$_smarty_tpl) : '';?>
</span>
                        <span class="white"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>80,'default'=>"MUSICREWARDS"),$_smarty_tpl) : '';?>
</span>
                        <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>81,'default'=>"Network"),$_smarty_tpl) : '';?>
</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php }
}
