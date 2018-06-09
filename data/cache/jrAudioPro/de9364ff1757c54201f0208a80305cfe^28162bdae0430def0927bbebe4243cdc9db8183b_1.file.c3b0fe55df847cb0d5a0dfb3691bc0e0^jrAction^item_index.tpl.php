<?php
/* Smarty version 3.1.31, created on 2018-05-28 08:52:27
  from "/webserver/mf6/data/cache/jrCore/c3b0fe55df847cb0d5a0dfb3691bc0e0^jrAction^item_index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b0bb53b707944_57552658',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '28162bdae0430def0927bbebe4243cdc9db8183b' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/c3b0fe55df847cb0d5a0dfb3691bc0e0^jrAction^item_index.tpl',
      1 => 1527493947,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b0bb53b707944_57552658 (Smarty_Internal_Template $_smarty_tpl) {
echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrAction",'assign'=>"murl"),$_smarty_tpl) : '';?>

<div class="block">

    <div class="title">
        <div class="block_config">

            <?php echo (function_exists('smarty_function_jrCore_item_index_buttons')) ? smarty_function_jrCore_item_index_buttons(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value),$_smarty_tpl) : '';?>


        </div>
        <h1><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"4",'default'=>"Timeline"),$_smarty_tpl) : '';?>
</h1>

        <div class="breadcrumbs">
            <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['profile_name']->value;?>
</a>
            &raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"4",'default'=>"Timeline"),$_smarty_tpl) : '';?>
</a>
            <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions']) && $_smarty_tpl->tpl_vars['_post']->value['profile_actions'] == 'mentions') {?>
            &raquo; <a href="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/mentions"><?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"7",'default'=>"Mentions"),$_smarty_tpl) : '';?>
</a>
            <?php }?>
        </div>
    </div>

    <?php if (jrProfile_is_profile_owner($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>

        <div class="block_content">

            <div id="action_search" class="item left p10" style="display:none;">
                <?php echo (function_exists('smarty_function_jrCore_lang')) ? smarty_function_jrCore_lang(array('module'=>"jrAction",'id'=>"8",'default'=>"Search",'assign'=>"svar"),$_smarty_tpl) : '';?>

                <?php if ($_smarty_tpl->tpl_vars['_post']->value['profile_actions'] == 'mentions') {?>
                    
                    <?php $_smarty_tpl->_assignInScope('where', 'mentions');
?>
                <?php } else { ?>
                    <?php $_smarty_tpl->_assignInScope('where', 'search');
?>
                <?php }?>
                <form name="action_search_form" action="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['profile_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['murl']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['where']->value;?>
" method="get" style="margin-bottom:0">
                    <input type="text" name="ss" placeholder="<?php echo $_smarty_tpl->tpl_vars['svar']->value;?>
" class="form_text">
                    <input type="submit" class="form_button" value="<?php echo $_smarty_tpl->tpl_vars['svar']->value;?>
">
                </form>
            </div>

            
            <?php if ($_smarty_tpl->tpl_vars['quota_jrAction_can_post']->value == 'on') {?>
                <div id="new_action" class="item">
                    <?php echo (function_exists('smarty_function_jrAction_form')) ? smarty_function_jrAction_form(array(),$_smarty_tpl) : '';?>

                </div>
            <?php }?>


            

            <?php $_smarty_tpl->_assignInScope('page_num', "1");
?>
            <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['p'])) {?>
                <?php $_smarty_tpl->_assignInScope('page_num', $_smarty_tpl->tpl_vars['_post']->value['p']);
?>
            <?php }?>

            
            <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions']) && $_smarty_tpl->tpl_vars['_post']->value['profile_actions'] == 'mentions') {?>

                <?php if (isset($_smarty_tpl->tpl_vars['_post']->value['ss']) && strlen($_smarty_tpl->tpl_vars['_post']->value['ss']) > 2) {?>
                    <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'search1'=>"action_mention_".((string)$_smarty_tpl->tpl_vars['_profile_id']->value)." = 1",'search2'=>"action_text like %".((string)$_smarty_tpl->tpl_vars['_post']->value['ss'])."%",'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>

                <?php } else { ?>
                    <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'search'=>"action_mention_".((string)$_smarty_tpl->tpl_vars['_profile_id']->value)." = 1",'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>

                <?php }?>

            <?php } elseif (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions']) && $_smarty_tpl->tpl_vars['_post']->value['profile_actions'] == 'search') {?>

                <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'search'=>"_item_id in ".((string)$_smarty_tpl->tpl_vars['_post']->value['match_ids']),'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>


            <?php } elseif (isset($_smarty_tpl->tpl_vars['_post']->value['profile_actions']) && $_smarty_tpl->tpl_vars['_post']->value['profile_actions'] == 'shared') {?>

                <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'search'=>"action_shared = ".((string)$_smarty_tpl->tpl_vars['_profile_id']->value),'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['_post']->value['p'],'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>


            <?php } else { ?>

                
                <?php if (jrUser_is_linked_to_profile($_smarty_tpl->tpl_vars['_profile_id']->value)) {?>
                    <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id desc",'pagebreak'=>12,'include_followed'=>true,'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>

                <?php } else { ?>
                    <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['page_num']->value,'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>

                <?php }?>

            <?php }?>

            <?php if (strlen($_smarty_tpl->tpl_vars['timeline']->value) > 10) {?>
            <div class="item" style="padding:0;border-bottom: none">
                <div id="timeline">
                    <?php echo $_smarty_tpl->tpl_vars['timeline']->value;?>

                </div>
            </div>
            <?php }?>

        </div>

    <?php } else { ?>

        <div class="block_content">
            <?php echo (function_exists('smarty_function_jrCore_list')) ? smarty_function_jrCore_list(array('module'=>"jrAction",'profile_id'=>$_smarty_tpl->tpl_vars['_profile_id']->value,'order_by'=>"_item_id desc",'pagebreak'=>12,'page'=>$_smarty_tpl->tpl_vars['_post']->value['p'],'pager'=>true,'assign'=>"timeline"),$_smarty_tpl) : '';?>

            <?php if (strlen($_smarty_tpl->tpl_vars['timeline']->value) > 10) {?>
            <div class="item">
                <div id="timeline">
                    <?php echo $_smarty_tpl->tpl_vars['timeline']->value;?>

                </div>
            </div>
            <?php }?>
        </div>

    <?php }?>

</div>

<?php }
}
