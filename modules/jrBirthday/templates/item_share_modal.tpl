{jrCore_module_url module="jrBirthday" assign="murl"}
<div id="birthday_share_modal">
    <div id="birthday_share_modal_box" class="item">

        {jrCore_module_url module="jrAction" assign="url"}
        <form action="{$jamroom_url}/{$url}/create_save" method="post" id="birthday_share_form">

            {jrCore_form_token assign="token"}
            <input type="hidden" name="jr_html_form_token" value="{$token}">
            <input type="hidden" name="jrAction_function" value="jrAction_quick_share_status_update">

            {jrCore_lang module="jrBirthday" id=4 default="Happy Birthday" assign="msg"}
            {$msg = "`$msg` @`$user_name`!"}
            <textarea name="action_text" id="birthday_update" class="form_textarea" rows="6" cols="72">{$msg}</textarea>
            <br>
            <span id="share_text_counter">{jrCore_lang module="jrAction" id=6 default="characters left"}: <span id="share_text_num">{$_conf['jrAction_max_length']|intval - $msg|strlen}</span></span>
            <br>

            {jrCore_module_url module="jrImage" assign="iurl"}
            {jrCore_lang module="jrAction" id=33 default="Share" assign="sv"}
            <img id="birthday_share_indicator" src="{$jamroom_url}/{$iurl}/img/skin/{$_conf.jrCore_active_skin}/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}"><input type="button" id="birthday_submit" value="{$sv|jrCore_entity_string}" class="form_button" onclick="jrBirthday_submit()">

        </form>

        <a id="birthday_share_modal_close" onclick="$.modal.close()">{jrCore_icon icon="close" size="16"}</a>

    </div>
</div>

<script type="text/javascript">
    $('.birthday_share').bind('click', function()
    {
        $('#birthday_share_modal').modal({
            onOpen: function(d)
            {
                d.overlay.fadeIn(75, function() {
                    d.container.show();
                    d.data.fadeIn(75, function()
                    {
                        var u = $('#birthday_update');
                        u.val(u.val() + "\n").focus();
                    });
                });
            }
        });
    });
    $('#birthday_update').shareCharCount( { allowed: {$_conf.jrAction_max_length|intval} , warning: 20 } );
</script>