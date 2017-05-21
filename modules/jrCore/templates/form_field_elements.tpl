<tr id="ff-row-{$name}"{if isset($row_style) && strlen($row_style) > 0} style="{$row_style}"{/if}>
    <td class="element_left form_input_left {$type}_left {$name}_element_left">
        <a id="ff-{$name}"></a>{$label}
        {if isset($sublabel) && strlen($sublabel) > 0}
            <br>
            <span class="sublabel">{$sublabel}</span>
        {/if}
    </td>
    <td class="element_right form_input_right {$type}_right {$name}_element_right" style="position:relative">
        {$html}
        {if $type == 'textarea' && !isset($theme)}
            <a class="form_textarea_expand" onclick="var e=$(this).prev();var h=e.height() + 100;e.animate( { height: h +'px' } , 250);">{jrCore_icon icon="arrow-down" size="16"}</a>
        {/if}
        {if isset($help) && strlen($help) > 0}
            <input type="button" value="?" class="form_button form_help_button" title="{jrCore_lang module="jrCore" id=34 default="expand help"}" onclick="$('#h_{$name}').slideToggle(250);">
        {/if}
    </td>
</tr>
{if isset($help) && strlen($help) > 0 && $type != 'editor'}
    <tr>
        <td class="element_left form_input_left" style="padding:0;height:0"></td>
        <td>
            <div id="h_{$name}" class="form_help" style="display:none">

                <table class="form_help_drop">
                    <tr>
                        <td class="form_help_drop_left">
                            {$help}
                            {* we only show updated time on Global / Quota config entries *}
                            {if isset($show_update_in_help) && $show_update_in_help == '1'}
                                {if isset($default) && !is_array($default) && strlen($default) > 0}
                                    {if isset($default_label)}
                                        {* default label will be set on select and checkbox items *}
                                        <br>
                                        <span class="form_help_default">Default: {$default_label}</span>
                                    {else}
                                        <br>
                                        <span class="form_help_default">Default: {$default|truncate:60}</span>
                                    {/if}
                                {/if}
                                {if $_conf.jrDeveloper_developer_mode == 'on' && strpos($_post._uri,'global')}
                                    <br>
                                    <br>
                                    Template Variable: <span class="fixed-width"><small>{ldelim}&#36;_conf.{$_post.module}_{$name}{rdelim}</small></span>
                                {/if}
                                {if isset($updated) && $updated > 0}
                                    <br>
                                    <span class="form_help_small">Last Updated: {$updated|jrCore_date_format} by {$user|default:"installer"}</span>
                                {/if}
                            {/if}
                        </td>
                        <td class="form_help_drop_right">
                            {* we only show updated time on Global / Quota config entries *}
                            {if isset($show_update_in_help) && $show_update_in_help == '1'}
                                {if isset($default) && !is_array($default) && strlen($default) > 0}
                                    {* we have to do things a bit differently for checkboxes *}
                                    {if isset($type) && $type == 'checkbox'}
                                        {if isset($default) && $default == "on"}
                                            <input type="button" value="use default" class="form_button" onclick="$('#{$name}').prop('checked','checked');">
                                        {else}
                                            <input type="button" value="use default" class="form_button" onclick="$('#{$name}').prop('checked','');">
                                        {/if}
                                    {else}
                                        {if $default == $value}
                                            <input type="button" value="use default" class="form_button form_button_disabled" disabled="disabled" title="Already using the Default Value">
                                        {else}
                                            <input type="button" value="use default" class="form_button" onclick="var v=$(this).val();if (v == 'use default'){ldelim}$('#{$name}').val('{$default_value}');$(this).val('cancel');{rdelim}else{ldelim}$('#{$name}').val('{$saved_value}');$(this).val('use default');{rdelim}">
                                        {/if}
                                    {/if}
                                {/if}
                            {/if}
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>
{/if}
