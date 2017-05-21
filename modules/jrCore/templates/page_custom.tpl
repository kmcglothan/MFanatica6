{if isset($label) && strlen($label) > 0}
    <tr>
        <td class="element_left form_input_left {$type}_left">
            {$label}{if isset($sublabel) && strlen($sublabel) > 0}<br><span class="sublabel">{$sublabel}</span>{/if}
        </td>
        <td class="element_right form_input_right {$type}_right" style="position:relative">
            {$html}
        {if isset($help) && strlen($help) > 0}
            <input type="button" value="?" class="form_button form_help_button" title="{jrCore_lang module="jrCore" id=34 default="expand help"}" onclick="$('#h_{$name}').slideToggle(250);">
        {/if}
        </td>
    </tr>
{else}
    <tr>
        <td colspan="2" class="element page_custom">{$html}</td>
    </tr>
{/if}
{if isset($help) && strlen($help) > 0}
    <tr>
        <td class="element_left form_input_left" style="padding:0;height:0"></td>
        <td>
            <div id="h_{$name}" class="form_help" style="display:none">

                <table class="form_help_drop">
                    <tr>
                        <td class="form_help_drop_left">
                            {$help}
                        </td>
                    </tr>
                </table>

            </div>
        </td>
    </tr>
{/if}
