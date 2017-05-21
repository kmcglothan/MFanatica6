{jrCore_module_url module="jrCustomForm" assign="murl"}
{if isset($form.form_message)}
    <div id="jrCustomForm_message" class="success center p10">{$form.form_message}</div>
{/if}
<div id="jrCustomForm_{$form.form_name}_msg" class="page_notice form_notice form_embed_notice"></div>
<form class="jrform" id="jrCustomForm_{$form.form_name}" name="jrCustomForm_{$form.form_name}" action="{$jamroom_url}/{$murl}/{$form.form_name}_save" method="post" accept-charset="utf-8" enctype="multipart/form-data">

    <input type="hidden" id="jr_html_form_token" name="jr_html_form_token" value="{$form_token}">

    {foreach $fields as $fl}

        {if jrCore_checktype($fl.label, "number_nz")}
            {jrCore_lang module="jrCustomForm" id=$fl.label assign="label"}
        {else}
            {assign var="label" value=$fl.label}
        {/if}

        {if $fl.type == "text"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            <input type="text" id="{$fl.name}" name="{$fl.name}" class="form_text form_embed_text" value="{$fl.default|jrCore_entity_string}">
            <br>

        {elseif $fl.type == "password"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            <input type="password" id="{$fl.name}" name="{$fl.name}" class="form_text form_embed_text" value="">
            <br>

        {elseif $fl.type == "textarea"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            <textarea id="{$fl.name}" name="{$fl.name}" class="form_textarea form_embed_textarea" value="{$fl.default|jrCore_entity_string}"></textarea>
            <br>

        {elseif $fl.type == "checkbox"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            <input type="hidden" name="{$fl.name}" value="off">
            {if isset($fl.default) && $fl.default == 'on'}
                <input type="checkbox" id="{$fl.name}" name="{$fl.name}" class="form_checkbox form_embed_checkbox" checked="checked">
            {else}
                <input type="checkbox" id="{$fl.name}" name="{$fl.name}" class="form_checkbox form_embed_checkbox">
            {/if}
            <br>

        {elseif $fl.type == "radio"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            {foreach $fl.options as $key => $val}
                {if isset($fl.default) && $fl.default == $key}
                    <input type="radio" id="{$fl.name}" name="{$fl.name}" value="{$key}" class="form_radio form_embed_radio" checked="checked">&nbsp;
                {else}
                    <input type="radio" id="{$fl.name}" name="{$fl.name}" value="{$key}" class="form_radio form_embed_radio">&nbsp;
                {/if}
                <span class="normal form_embed_sublabel">{$val|jrCore_entity_string}</span>
                <br>
            {/foreach}

        {elseif $fl.type == "notice"}
            <br>
            <div class="{$label} center p10">{$fl.options}</div>

        {elseif $fl.type == "optionlist"}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            {foreach $fl.options as $key => $val}
                {if isset($fl.default) && $fl.default == $key}
                    <input type="checkbox" id="{$fl.name}_{$key}" name="{$fl.name}_{$key}" class="form_checkbox form_embed_checkbox" checked="checked">&nbsp;
                {else}
                    <input type="checkbox" id="{$fl.name}_{$key}" name="{$fl.name}_{$key}" class="form_checkbox form_embed_checkbox">&nbsp;
                {/if}
                <span class="normal form_embed_sublabel">{$val|jrCore_entity_string}</span>
                <br>
            {/foreach}

        {elseif $fl.type == "file" && jrUser_is_logged_in()}
            <span class="normal form_embed_label">{$label}:</span>
            <br>
            {jrCore_upload_button module="jrCustomForm" field="{$fl.name}" allowed="{$fl.options|default:"zip,png,jpg,gif"}"}

        {elseif $fl.type == "select"}
            <span class="normal form_embed_label">{$label}:</span>
            <select id="{$fl.name}" name="{$fl.name}" class="form_select form_embed_select">
                {foreach $fl.options as $key => $val}
                    {if $key == $fl.default}
                        <option value="{$key}" selected="selected"> {$val}</option>
                    {else}
                        <option value="{$key}"> {$val}</option>
                    {/if}
                {/foreach}
            </select>

        {elseif $fl.type == "checkbox_spambot"}
            <script type="text/javascript">
                $(document).ready(function () { jrFormSpamBotCheckbox('{$fl.name}', {$fl.tab_order}) });
            </script>
            <span class="normal form_embed_label">{jrCore_lang module="jrUser" id=90 default="Human Check"}:</span>
            <span id="sb_{$fl.name}"></span>

        {/if}

    {/foreach}

    <div class="form_embed_submit_section">
        <img id="form_submit_indicator" src="{$jamroom_url}/{jrCore_module_url module="jrImage"}/img/skin/{$_conf.jrCore_active_skin}/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id=73 default="working..."}"><input type="button" id="jrCustomForm_{$form.form_name}_submit" class="form_button form_embed_button" value="{$submit_value}" onclick="jrFormSubmit('#jrCustomForm_{$form.form_name}','{$form_token}','ajax');">
    </div>

</form>
<script>
    $(document).ajaxSuccess(function (event, request, settings, data) {
        if (typeof data.notices !== 'undefined' && data.notices[0].type === 'success') {
            $('#jrCustomForm_{$form.form_name}').fadeOut();
        }
    });
</script>
