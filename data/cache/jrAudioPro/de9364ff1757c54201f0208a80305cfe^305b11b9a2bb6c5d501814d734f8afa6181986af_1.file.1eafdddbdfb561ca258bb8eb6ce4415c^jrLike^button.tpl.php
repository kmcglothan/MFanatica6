<?php
/* Smarty version 3.1.31, created on 2018-05-21 23:28:08
  from "/webserver/mf6/data/cache/jrCore/1eafdddbdfb561ca258bb8eb6ce4415c^jrLike^button.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0347f8d80014_16861317',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '305b11b9a2bb6c5d501814d734f8afa6181986af' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/1eafdddbdfb561ca258bb8eb6ce4415c^jrLike^button.tpl',
      1 => 1526941688,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0347f8d80014_16861317 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['action']->value == 'like') {?>

    <div id="l<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="like_button_box">

        <?php if ($_smarty_tpl->tpl_vars['like_status']->value == 'like') {?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>4,'default'=>"Like",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrLike_require_login'] == 'on' && !jrUser_is_logged_in()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>"like.png",'width'=>"24",'height'=>"24",'class'=>"like_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php } else { ?>
                <a onclick="jrLike_action('<?php echo $_smarty_tpl->tpl_vars['module_url']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
', 'like', '<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
');"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>"like.png",'width'=>"24",'height'=>"24",'class'=>"like_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php }?>


        <?php } elseif ($_smarty_tpl->tpl_vars['like_status']->value == 'liked') {?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>6,'default'=>"You Liked This!",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrLike_require_login'] == 'on' && !jrUser_is_logged_in()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>"liked.png",'width'=>"24",'height'=>"24",'class'=>"like_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php } else { ?>
                <a onclick="jrLike_action('<?php echo $_smarty_tpl->tpl_vars['module_url']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
', 'like', '<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
');"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>"liked.png",'width'=>"24",'height'=>"24",'class'=>"like_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php }?>

        <?php } else { ?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>4,'default'=>"Like",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['like_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"like_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>


        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['like_count']->value > 0) {?>
        <span id="lc<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="like_count"><a onclick="jrLike_get_like_users(this,'<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
','like','<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
')"><?php echo number_format($_smarty_tpl->tpl_vars['like_count']->value);?>
</a></span>
        <?php } else { ?>
        <span id="lc<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="like_count">0</span>
        <?php }?>

    </div>

<?php } elseif ($_smarty_tpl->tpl_vars['action']->value == 'dislike') {?>

    <div id="d<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="dislike_button_box">

        <?php if ($_smarty_tpl->tpl_vars['dislike_status']->value == 'dislike') {?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>5,'default'=>"Dislike",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrLike_require_login'] == 'on' && !jrUser_is_logged_in()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['dislike_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"dislike_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php } else { ?>
                <a onclick="jrLike_action('<?php echo $_smarty_tpl->tpl_vars['module_url']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
', 'dislike', '<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
');"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['dislike_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"dislike_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php }?>

        <?php } elseif ($_smarty_tpl->tpl_vars['dislike_status']->value == 'disliked') {?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>7,'default'=>"You Disliked This!",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php if ($_smarty_tpl->tpl_vars['_conf']->value['jrLike_require_login'] == 'on' && !jrUser_is_logged_in()) {?>
                <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrUser",'assign'=>"uurl"),$_smarty_tpl) : '';?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['uurl']->value;?>
/login"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['dislike_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"dislike_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php } else { ?>
                <a onclick="jrLike_action('<?php echo $_smarty_tpl->tpl_vars['module_url']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
', 'dislike', '<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
');"><?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['dislike_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"dislike_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>
</a>
            <?php }?>

        <?php } else { ?>

            
            <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrLike",'id'=>5,'default'=>"Dislike",'assign'=>"title"),$_smarty_tpl) : '';?>

            <?php echo (function_exists('smarty_function_jrCore_image')) ? smarty_function_jrCore_image(array('module'=>"jrLike",'image'=>((string)$_smarty_tpl->tpl_vars['dislike_status']->value).".png",'width'=>"24",'height'=>"24",'class'=>"dislike_button_img",'alt'=>$_smarty_tpl->tpl_vars['title']->value,'title'=>$_smarty_tpl->tpl_vars['title']->value),$_smarty_tpl) : '';?>


        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['dislike_count']->value > 0) {?>
        <span id="dc<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="dislike_count"><a onclick="jrLike_get_like_users(this,'<?php echo $_smarty_tpl->tpl_vars['module']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['item']->value['_item_id'];?>
','dislike','<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
')"><?php echo number_format($_smarty_tpl->tpl_vars['dislike_count']->value);?>
</a></span>
        <?php } else { ?>
        <span id="dc<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="dislike_count">0</span>
        <?php }?>

    </div>
<?php }?>

<div id="likers-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="search_box likers_box">
    <div id="liker_list_<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" class="liker_list"></div>
    <div class="clear"></div>
    <div style="position:absolute;right:6px;bottom:6px">
        <a class="simplemodal-close"><?php echo (function_exists('smarty_function_jrCore_icon')) ? smarty_function_jrCore_icon(array('icon'=>"close",'size'=>"16"),$_smarty_tpl) : '';?>
</a>
    </div>
</div>

<div id="like-state-<?php echo $_smarty_tpl->tpl_vars['unique_id']->value;?>
" style="display:none"></div>
<?php }
}
