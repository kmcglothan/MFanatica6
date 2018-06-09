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
            {jrCore_module_url module="jrImage" assign="url"}
            <img src="{$jamroom_url}/{$url}/img/skin/{$_conf.jrCore_active_skin}/logo.png?_v={$smarty.now}" title="{$_conf.jrCore_system_name}" alt="{$_conf.jrCore_system_name}" width="250">
        </th>
    </tr>
    <tr>
        <td style="background-color:#FFFFFF;text-align: left">

            {* email message to user *}
            {$email_message}

        </td>
    </tr>
    <tr>
        <td style="font-size:13px;color:#999999;text-align:center;">

            {* email preferences *}
            {$email_preferences}
            <br><br>
            Please add our email address to your Address Book so our messages don't end up in spam.<br>Thank You!
            <br>
            &copy;{$smarty.now|date_format:"%Y"} {$_conf.jrCore_system_name}

        </td>
    </tr>
    </tbody>
</table>
