{jrUser_whos_online template="whos_online.tpl" assign="WHOS_ONLINE"}
{if isset($WHOS_ONLINE) && strlen($WHOS_ONLINE) > 0}
    {$WHOS_ONLINE}
{else}
    <div style="text-align:center;">
        <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="178" default="Sorry, No Users Online!"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="179" default="You Can Help Make This Site"}<br>{jrCore_lang skin=$_conf.jrCore_active_skin id="180" default="Active By Logging In!"}</h4><br>
        <br>
        <input type="button" class="form_button" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="6" default="login"}" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/login');"><br>
        <br>
        {if $_conf.jrCore_maintenance_mode != 'on' && $_conf.jrUser_signup_on == 'on'}
            <h4>{jrCore_lang skin=$_conf.jrCore_active_skin id="181" default="OR"}</h4><br>
            <br>
            <input type="button" class="form_button" onclick="jrCore_window_location('{$jamroom_url}/{jrCore_module_url module="jrUser"}/signup');" value="{jrCore_lang  skin=$_conf.jrCore_active_skin id="2" default="create"}&nbsp;{jrCore_lang  skin=$_conf.jrCore_active_skin id="3" default="account"}"><br>
            <br>
        {/if}
    </div>
{/if}
