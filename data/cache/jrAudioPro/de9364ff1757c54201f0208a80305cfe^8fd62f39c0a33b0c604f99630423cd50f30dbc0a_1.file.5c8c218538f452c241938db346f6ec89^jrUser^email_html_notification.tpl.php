<?php
/* Smarty version 3.1.31, created on 2018-05-21 01:07:51
  from "/webserver/mf6/data/cache/jrCore/5c8c218538f452c241938db346f6ec89^jrUser^email_html_notification.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5b020dd7d79502_47860706',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '8fd62f39c0a33b0c604f99630423cd50f30dbc0a' => 
    array (
      0 => '/webserver/mf6/data/cache/jrCore/5c8c218538f452c241938db346f6ec89^jrUser^email_html_notification.tpl',
      1 => 1526861271,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5b020dd7d79502_47860706 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once '/webserver/mf6/modules/jrCore/contrib/smarty/libs/plugins/modifier.date_format.php';
?>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial scale=1.0">
    <style type="text/css">
        table {
            background-color: #888888;
            width: 780px;
        }

        img {
            width: 250px;
        }

        th {
            background-color: #444444;
            color: #FFFFFF;
        }

        td {
            background-color: #F3F3F3;
            font-size: 15px;
        }

        .bbcode_quote {
            width: 100%;
            padding: 10px;
            background-color: #EEEEEE;
            font-size: 13px;
            font-style: italic;
            box-sizing: border-box;
        }

        .bbcode_quote_user {
            font-size: 16px;
            font-weight: bold;
        }

        .bbcode_code {
            font-family: monospace;
            width: 100%;
            padding: 0 10px;
            font-size: 13px;
            box-sizing: border-box;
            background-color: #EEEEEE;
            border-radius: 3px;
        }

        @media only screen and (max-width: 767px) {
            body {
                text-align: center;
            }

            table {
                width: 100%;
            }

            th {
                text-align: center;
            }

            img {
                width: 50%;
            }
        }
    </style>
</head>
<body>
<table border="0" cellspacing="1" cellpadding="20">
    <tbody>
    <tr>
        <th>
            <?php echo (function_exists('smarty_function_jrCore_module_url')) ? smarty_function_jrCore_module_url(array('module'=>"jrImage",'assign'=>"url"),$_smarty_tpl) : '';?>

            <img src="<?php echo $_smarty_tpl->tpl_vars['jamroom_url']->value;?>
/<?php echo $_smarty_tpl->tpl_vars['url']->value;?>
/img/skin/<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_active_skin'];?>
/logo.png?_v=<?php echo time();?>
" title="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>
" width="250">
        </th>
    </tr>
    <tr>
        <td style="background-color:#FFFFFF;text-align: left">

            
            <?php echo $_smarty_tpl->tpl_vars['email_message']->value;?>


        </td>
    </tr>
    <tr>
        <td style="font-size:13px;color:#999999;text-align:center;">

            
            <?php echo $_smarty_tpl->tpl_vars['email_preferences']->value;?>

            <br><br>
            Please add our email address to your Address Book so our messages don't end up in spam.<br>Thank You!
            <br>
            &copy;<?php echo smarty_modifier_date_format(time(),"%Y");?>
 <?php echo $_smarty_tpl->tpl_vars['_conf']->value['jrCore_system_name'];?>


        </td>
    </tr>
    </tbody>
</table>
<?php }
}
