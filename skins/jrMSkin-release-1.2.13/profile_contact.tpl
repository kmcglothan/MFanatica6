<div class="box">
    {jrMSkin_sort template="icons.tpl" nav_mode="contact" profile_url=$profile_url}
    <div class="box_body">
        <div class="wrap">
            <div class="media">
                <textarea class="form_textarea" id="contact_message" rows="2" placeholder="Type a message..."></textarea>
                <input type="hidden" id="message_profile_id" value="{$_profile_id}" />
                <div style="text-align: right; padding: 5px;">
                    {jrCore_module_url module="jrImage" assign="iurl"}
                    <span class="message_result"></span>
                    <img width="24" height="24" alt="working..." src="{$jamroom_url}/{$iurl}/img/skin/jrMSkin/submit.gif" id="contact_submit_indicator" style="display: none;">
                    <button disabled="disabled" class="form_button" id="contact_button">Send</button>
                </div>
            </div>
        </div>
    </div>
</div>