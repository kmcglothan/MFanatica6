<table>
    <tr>
        <td class="element_left form_input_left user_notification_left">{$help}</td>
        <td class="element_right form_input_right user_notification_right">
            <input type="radio" class="form_radio" name="{$name}" value="off" tabindex="2" {$off_checked}>&nbsp;{jrCore_lang module="jrUser" id=65 default="Do Not Notify"}
            <br>
            <input type="radio" class="form_radio" name="{$name}" value="email" tabindex="2" {$email_checked}>&nbsp;{jrCore_lang module="jrUser" id=66 default="Email"}
            {if jrCore_module_is_active('jrPrivateNote') && (!isset($_options.email_only) || $_options.email_only !== true)}
            <br>
            <input type="radio" class="form_radio" name="{$name}" value="note" tabindex="2" {$note_checked}>&nbsp;{jrCore_lang module="jrUser" id=67 default="Private Note"}
            {/if}
        </td>
    </tr>
</table>

