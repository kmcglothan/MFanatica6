<style type="text/css">
* {
    -webkit-font-smoothing: antialiased;
}
img.bg {
    min-height: 100%;
    min-width: 1024px;
    width: 100%;
    height: 100%;
    position: fixed;
    top: 0;
    left: 0;
}
.wrap {
    position: fixed;
    top: 50px;
    left: 50px;
    width: 90%;
}
.message {
    margin: 12px;
    padding: 20px 6px;
    font-size: 18px;
    color: #FFF;
    text-shadow: 1px 1px 1px #000;
}
#launch_form_box {
    margin: 12px;
    padding: 16px 0 16px 6px;
}
#launch_notice {
    padding-bottom: 12px;
    color: #FFFFFF;
    text-shadow: 1px 1px 1px #000;
}
.launch_email {
    width: 400px;
    background: transparent;
    padding: 10px;
    font-size: 18px;
    border: 2px solid #FFFFFF;
    color: #FFFFFF;
    border-radius: 3px;
    margin-right: 12px;
}
.launch_email:focus {
    background: transparent;
    padding: 10px;
    font-size: 18px;
    border: 2px solid #FFCC00;
    border-radius: 3px;
}
.launch_email_error {
    border: 2px solid #FFCC00;
    color: #FFCC00;
}
.launch_submit_button {
    display: inline-block;
    background: transparent;
    padding: 10px 20px 10px 10px;
    margin-left: 10px;
    font-size: 18px;
    border: 2px solid #FFFFFF;
    color: #FFFFFF;
    text-transform: capitalize;
    border-radius: 25px;
}
.lsb_disabled {
    display: inline-block;
    background: transparent;
    padding: 10px 20px;
    font-size: 18px;
    border: 2px solid #666666;
    color: #666666;
    text-transform: capitalize;
    border-radius: 25px;
}
.content {
    zoom: 1;
    margin: 12px;
    opacity: 1;
    border-radius: 12px;
}
.content h1 {
    font-size: 48px;
    padding: 0;
    color: #FFF;
    text-align: center;
    text-shadow: 1px 1px 1px #000;
}
.login_link {
    position: fixed;
    bottom: 48px;
    right: 48px;
    margin: 36px 0 0 12px;
    font-size: 15px;
    color #999999;
    text-shadow: 1px 1px 1px #000;
}
.login_link a {
    color: #FFF;
}
.login_link a:hover {
    text-decoration: none;
}

@media screen and (max-width: 1024px) {
    img.bg {
        left: 50%;
        margin-left: -512px;
    }
    .wrap {
        top: 0;
        left: 0;
        width: 90%;
    }
    .message {
        font-size: 14px;
    }
    .content h1 {
        font-size: 28px;
    }
}
@media screen and (max-width: 767px) {
    #launch_form_box {
        margin: 0 auto;
        text-align: center;
    }
    .launch_email {
        margin: 0 auto;
        width: 200px;
    }
    .launch_submit_button {
        margin-top: 20px;
        width: 200px;
    }
    .login_link {
        text-align: center;
        right: 0;
        left: 0;
        bottom: 10px;
    }
}
</style>

<body style="background:#000;">

{jrCore_form_token assign="token"}
{jrCore_module_url module="jrLaunch" assign="murl"}
{jrCore_lang module="jrLaunch" id="1" assign="email"}
{jrCore_image module="jrLaunch" image="background.jpg" class="bg" alt=""}

<div class="wrap">

    <div class="content">
        <h1>{$_conf.jrLaunch_launch_title|default:$_conf.jrCore_system_name}</h1>
    </div>

    {if !empty($_conf.jrLaunch_launch_description)}
    <div class="message">
        {$_conf.jrLaunch_launch_description|nl2br}
    </div>
    {/if}

    <div id="launch_form_box">
        <div id="launch_notice">&nbsp;</div>
        <form id="launch_form" action="{$jamroom_url}/{$murl}/signup" onsubmit="jrLaunch_signup();return false">
            <input type="hidden" name="jr_html_form_token" value="{$token}">
            <input type="text" name="launch_email_address" class="launch_email" placeholder="{$email|jrCore_entity_string}"><input type="button" class="launch_submit_button" value="{jrCore_lang module="jrLaunch" id="2"}" tabindex="2" onclick="jrLaunch_signup()">
        </form>
    </div>

</div>

<div class="login_link">
    {jrCore_module_url module="jrUser" assign="url"}
    <a href="{$jamroom_url}/{$url}/login">{jrCore_lang module="jrLaunch" id=7 default="Admin Login"}</a>
</div>

</body>
</html>
