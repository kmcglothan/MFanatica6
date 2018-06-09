<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:32
  from "/webserver/mf6/data/cache/jrCore/b8bb167a05a688dab0c4df98f745ec5f^jrAudioPro^footer.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb54085f779_63970773',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '9bd1776f4305f91783a951f622f0f82f8840d618' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/b8bb167a05a688dab0c4df98f745ec5f^jrAudioPro^footer.tpl',
      1 => 1527493952,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb54085f779_63970773 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.date_format.php';
if (strlen($_smarty_tpl->tpl_vars['page_template']->value) == 0) {?>
</div>
<?php }?>
</div>

<div id="footer">
    <div id="footer_content">
        <div class="container">

            <div class="row">
                
                <div class="col6">
                    <div class="table">
                        <div class="table-row">
                            <div class="table-cell">
                                <ul class="social clearfix">
                                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_facebook_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_facebook_url'] != "0") {?>
                                        <?php if (strpos($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_facebook_url'],"https:") !== false) {?>
                                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_facebook_url'];?>
" class="social-facebook" target="_blank"></a></li>
                                        <?php } else { ?>
                                            <li><a href="https://facebook.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_facebook_url'];?>
" class="social-facebook" target="_blank"></a></li>
                                        <?php }?>
                                    <?php }?>
                                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_twitter_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_twitter_url'] != "0") {?>
                                        <?php if (strpos($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_twitter_url'],"https:") !== false) {?>
                                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_twitter_url'];?>
" class="social-twitter" target="_blank"></a></li>
                                        <?php } else { ?>
                                            <li><a href="https://twitter.com/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_twitter_url'];?>
" class="social-twitter" target="_blank"></a></li>
                                        <?php }?>
                                    <?php }?>
                                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_google_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_google_url'] != "0") {?>
                                        <?php if (strpos($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_google_url'],"https:") !== false) {?>
                                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_google_url'];?>
" class="social-google" target="_blank"></a></li>
                                        <?php } else { ?>
                                            <li><a href="https://plus.google.com/u/0/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_google_url'];?>
" class="social-google" target="_blank"></a></li>
                                        <?php }?>
                                    <?php }?>
                                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_linkedin_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_linkedin_url'] != "0") {?>
                                        <?php if (strpos($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_linkedin_url'],"https:") !== false) {?>
                                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_linkedin_url'];?>
" class="social-linkedin" target="_blank"></a></li>
                                        <?php } else { ?>
                                            <li><a href="https://linkedin.com/in/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_linkedin_url'];?>
" class="social-linkedin" target="_blank"></a></li>
                                        <?php }?>
                                    <?php }?>
                                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_youtube_url']) > 0 && $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_youtube_url'] != "0") {?>
                                        <?php if (strpos($_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_youtube_url'],"https:") !== false) {?>
                                            <li><a href="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_youtube_url'];?>
" class="social-youtube" target="_blank"></a></li>
                                        <?php } else { ?>
                                            <li><a href="https://www.youtube.com/channel/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrAudioPro_youtube_url'];?>
" class="social-youtube" target="_blank"></a></li>
                                        <?php }?>
                                    <?php }?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="col6 last">
                    <div id="footer_text">
                        &copy;<?php echo smarty_modifier_date_format(time(),"%Y");?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
</a><br>
                        
                        <?php echo (function_exists('smarty_function_jrCore_powered_by')) ? smarty_function_jrCore_powered_by(array(),$_smarty_tpl) : '';?>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

</div>

<a href="#" id="scrollup" class="scrollup"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"arrow-up"),$_smarty_tpl) : '';?>
</a>



<?php echo '<script'; ?>
 type="text/javascript">
    (function($) {
        $(document).ready(function() {
            var ms = new $.slidebars();
            $('li#menu_button > a').on('click', function() {
                ms.slidebars.toggle('left');
            });
        });
    }) (jQuery);
<?php echo '</script'; ?>
>

<?php if (isset($_smarty_tpl->tpl_vars['css_footer_href']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['css_footer_href']->value, '_css');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_css']->value) {
?>
        <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['_css']->value['source'];?>
" media="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_css']->value['media'])===null||strlen($tmp)===0||$tmp==='' ? "screen" : $tmp);?>
"/>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
if (isset($_smarty_tpl->tpl_vars['javascript_footer_href']->value)) {?>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['javascript_footer_href']->value, '_js');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['_js']->value) {
?>
        <?php echo '<script'; ?>
 type="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['_js']->value['type'])===null||strlen($tmp)===0||$tmp==='' ? "text/javascript" : $tmp);?>
" src="<?php echo $_smarty_tpl->tpl_vars['_js']->value['source'];?>
"><?php echo '</script'; ?>
>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

<?php }
if (isset($_smarty_tpl->tpl_vars['javascript_footer_function']->value)) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
        <?php echo $_smarty_tpl->tpl_vars['javascript_footer_function']->value;?>

    <?php echo '</script'; ?>
>
<?php }?>


<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/jrAudioPro/js/css3-animate-it.js"><?php echo '</script'; ?>
>

</body>
</html>
<?php }
}
