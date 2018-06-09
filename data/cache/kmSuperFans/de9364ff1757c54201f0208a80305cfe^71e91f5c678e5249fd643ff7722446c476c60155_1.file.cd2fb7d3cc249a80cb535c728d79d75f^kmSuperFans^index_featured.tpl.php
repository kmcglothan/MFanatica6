<?php
/* Smarty version 3.1.31, created on 2018-06-08 23:46:10
  from "/webserver/mf6/data/cache/jrCore/cd2fb7d3cc249a80cb535c728d79d75f^kmSuperFans^index_featured.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b1b07329183a9_82389186',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '71e91f5c678e5249fd643ff7722446c476c60155' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/cd2fb7d3cc249a80cb535c728d79d75f^kmSuperFans^index_featured.tpl',
      1 => 1528497970,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b1b07329183a9_82389186 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_1_active'] != 'off') {?>
    <section class="featured">
        <div class="row">
            <div class="col12">
                <div class="center">
                    <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>56,'default'=>"We have the world's best music from the world's coolest community"),$_smarty_tpl) : '';?>
</h1>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col12">
                <div class="head">
                    <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"audio",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>48,'default'=>"On Sale Now"),$_smarty_tpl) : '';?>
</span>
                </div>
                <div class="list">
                    <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_1'] == 'on') {?>
                        <?php $_smarty_tpl->_assignInScope('s1', "audio_file_item_price > 0");
?>
                    <?php }?>
                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_1_ids']) > 0) {?>
                        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAudio",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_1_ids']),'limit'=>"8",'template'=>"index_item_1.tpl"),$_smarty_tpl) : '';?>

                    <?php } elseif (jrCore_module_is_active('jrCombinedAudio') && $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_1_soundcloud'] == 'on') {?>
                        <?php echo (function_exists('smarty_function_jrCombinedAudio_get_active_modules')) ? smarty_function_jrCombinedAudio_get_active_modules(array('assign'=>"mods"),$_smarty_tpl) : '';?>

                        <?php if (strlen($_smarty_tpl->tpl_vars['mods']->value) > 0) {?>
                            <?php echo (function_exists('smarty_function_jrSeamless_list')) ? smarty_function_jrSeamless_list(array('modules'=>$_smarty_tpl->tpl_vars['mods']->value,'search'=>$_smarty_tpl->tpl_vars['s1']->value,'order_by'=>"_created desc",'limit'=>"8",'template'=>"index_item_1.tpl"),$_smarty_tpl) : '';?>

                        <?php } elseif (jrUser_is_admin()) {?>
                            No active audio modules found!
                        <?php }?>
                    <?php } else { ?>
                        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAudio",'search'=>$_smarty_tpl->tpl_vars['s1']->value,'limit'=>"8",'template'=>"index_item_1.tpl",'require_image'=>"audio_image"),$_smarty_tpl) : '';?>

                    <?php }?>
                </div>
            </div>
        </div>
    </section>
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_2_active'] != 'off') {?>
    <section class="featured dark">
        <div class="row">
            <div class="col12">
                <div class="head">
                    <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"star",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <span><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>49,'default'=>"Featured Artists"),$_smarty_tpl) : '';?>
</span>
                </div>
                <div class="col12">
                    <?php if (strlen($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_2_ids']) > 0) {?>
                        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrProfile",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_2_ids']),'limit'=>"7",'template'=>"index_item_2.tpl"),$_smarty_tpl) : '';?>

                    <?php } else { ?>
                        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrProfile",'order_by'=>"profile_jrAudio_item_count numerical_desc",'limit'=>"7",'template'=>"index_item_2.tpl"),$_smarty_tpl) : '';?>

                    <?php }?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col12">
                <div class="center register">
                    <br>
                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>52,'default'=>"Join us today and start creating."),$_smarty_tpl) : '';?>
 <button class="form_button" onclick="jrCore_window_location('<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser"),$_smarty_tpl) : '';?>
/signup')">Register</button>
                </div>
            </div>
        </div>
    </section>
<?php }?>


<?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_list_3_active'] != 'off') {?>

    <section class="featured">
        <div class="row">
            <div class="col12">
                <div class="head">
                    <?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"stats",'size'=>"20",'color'=>"ff5500"),$_smarty_tpl) : '';?>
 <span> <?php echo $_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'];?>
 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>50,'default'=>"Day Charts"),$_smarty_tpl) : '';?>
</span>
                </div>
                <div class="index_chart">
                    <div class="list_item">
                        <div class="table center">
                            <div class="table-row">
                                <div class="table-cell" style="width: 50px;">
                                    #
                                </div>
                                <div class="table-cell" style="width: 50px;">
                                    dir
                                </div>
                                <div class="table-cell desk" style="width: 50px;">
                                    last
                                </div>
                                <div class="table-cell desk" style="width: 50px">
                                    &nbsp;
                                </div>
                                <div class="table-cell desk" style="width: 42px;">
                                    &nbsp;
                                </div>
                                <div class="table-cell">
                                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"30",'default'=>"Title"),$_smarty_tpl) : '';?>

                                </div>
                                <div class="table-cell desk" style="width: 220px">
                                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"31",'default'=>"Artist"),$_smarty_tpl) : '';?>

                                </div>

                                <div class="table-cell desk" style="width: 120px;">
                                    Genre
                                </div>
                                <div class="table-cell desk" style="width: 80px;">
                                    <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>"58",'default'=>"Plays"),$_smarty_tpl) : '';?>

                                </div>
                                <div class="table-cell chart_buttons" style="width:150px; text-align: right; position: relative;">
                                    <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"iurl"),$_smarty_tpl) : '';?>

                                    <div id="chartLoader" class="p10" style="display:none"><img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/skins/kmSuperFans/img/ajax-loader.gif" alt="<?php echo jrCore_entity_string($_smarty_tpl->tpl_vars['working']->value);?>
"></div>
                                    <select class="form_select" id="chart_days" onchange="kmSuperFans_chart_days(this.value)">
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '1') {?>selected="selected"<?php }?> value="1">1 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '7') {?>selected="selected"<?php }?> value="7">7 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '14') {?>selected="selected"<?php }?> value="14">14 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '30') {?>selected="selected"<?php }?> value="30">30 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '90') {?>selected="selected"<?php }?> value="90">90 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                        <option <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'] == '365') {?>selected="selected"<?php }?> value="365">365 <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('skin'=>"kmSuperFans",'id'=>61,'default'=>"Days"),$_smarty_tpl) : '';?>
</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="list" id="chart">
                        <?php if ($_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_require_price_3'] == 'on') {?>
                            <?php $_smarty_tpl->_assignInScope('s2', "audio_file_item_price > 0");
?>
                        <?php }?>
                        <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAudio",'chart_field'=>"audio_file_stream_count",'search'=>$_smarty_tpl->tpl_vars['s2']->value,'chart_days'=>$_smarty_tpl->tpl_vars['_conf']->value['kmSuperFans_chart_days'],'limit'=>"17",'template'=>"index_chart_item.tpl"),$_smarty_tpl) : '';?>

                    </div>
                </div>
            </div>
        </div>
    </section>
<?php }
}
}
