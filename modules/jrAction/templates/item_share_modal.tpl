{jrCore_module_url module="jrAction" assign="murl"}
<div id="share_modal_box" class="item">
    <a id="share_modal_close" onclick="$.modal.close()">{jrCore_icon icon="close" size="16"}</a>
    <form action="{$jamroom_url}/{$murl}/share/{$module}/{$item_id}" method="post" id="share_form">

        {jrCore_form_token assign="token"}
        <input type="hidden" value="{$module}" name="share_module">
        <input type="hidden" value="{$token}" name="jr_html_form_token">
        <textarea placeholder="{jrCore_lang module="jrAction" id=32 default="Add a Comment"}" name="share_text" id="share_update" class="form_textarea" rows="6" cols="72"></textarea>
        <span id="share_text_counter">{jrCore_lang module="jrAction" id=6 default="characters left"}: <span id="share_text_num">{$_conf.jrAction_max_length|intval}</span></span>

        <div id="share_item_box">
            {if $template}
                {jrCore_list module=$module search="_item_id = `$item_id`" limit=1 template=$template}
            {else}
                {jrCore_list module=$module search="_item_id = `$item_id`" limit=1}
            {/if}
        </div>

        {jrCore_lang module="jrCore" id="73" default="working..." assign="working"}
        <img width="24" height="24" alt="{$working|jrCore_entity_string}" src="{$jamroom_url}/image/img/skin/{$_conf.jrCore_active_skin}/form_spinner.gif" id="share_submit_indicator">
        {jrCore_lang module="jrAction" id=33 default="Share" assign="sv"}
        <input type="button" onclick="jrAction_share_save()" value="{$sv|jrCore_entity_string}" class="form_button" id="share_submit">

    </form>
</div>

<script type="text/javascript">
    $('#share_update').shareCharCount({ allowed: {$_conf.jrAction_max_length|intval} , warning: 20 });
</script>

