{jrCore_include template="meta.tpl"}

<body style="position:inherit;background:#FFFFFF;">

<style type="text/css">
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
    position: absolute;
    top: 50px;
    left: 50px;
    width: 800px;
}
.message {
    margin: 24px 12px;
    font-size: 18px;
    color: #000;
}
.content {
    zoom: 1;
    margin: 12px;
    opacity: 1;
    border-radius: 12px;
    text-align: left;
}
.content h1 {
    display:inline;
    font-size: 48px;
    padding: 0;
    color: #000;
    text-align: center;
}
.login_link {
    position: absolute;
    bottom: 48px;
    right: 48px;
    display: inline-block;
    margin: 36px 0 0 12px;
    font-size: 14px;
}
.login_link a {
    color: #000;
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
        width: 100%;
    }
    .message {
        font-size: 14px;
    }
    .content h1 {
        font-size: 28px;
    }
}
</style>

{jrCore_image module="jrCore" image="maintenance-bg.jpg" class="bg" alt=""}

<div class="wrap">

    <div class="content">
        <h1>{$_conf.jrCore_system_name}</h1>
    </div>

    <div class="message">
        {$_conf.jrCore_maintenance_notice|nl2br|jrCore_string_to_url}
    </div>

</div>

<div class="login_link">
    {jrCore_module_url module="jrUser" assign="url"}
    <a href="{$jamroom_url}/{$url}/login">{jrCore_lang module="jrUser" id=110 default="Click Here to Log In"}</a>
</div>

</body>
</html>
