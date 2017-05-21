<table>
    <tr>
        <td class="element_left search_area_left">{jrCore_lang module="jrCore" id="8" default="Search"}</td>
        <td class="element_right search_area_right">
            <input type="text" onkeypress="var s=$('#jrembed_ss').val(); if (event && event.keyCode == 13 && s.length > 0) { var m=$('#jrembed_amod').text(); jrEmbed_load_module(m, 1, s); }" value="" class="form_text form_text_search" id="jrembed_ss" name="ss">
            <input type="button" onclick="var s=$('#jrembed_ss').val(); var m=$('#jrembed_amod').text(); jrEmbed_load_module(m, 1, s);" class="form_button" value="{jrCore_lang module="jrCore" id="8" default="Search"}">
            <input type="button" onclick="$('#jrembed_ss').val(''); var m=$('#jrembed_amod').text(); jrEmbed_load_module(m, 1, '');" class="form_button" value="{jrCore_lang module="jrCore" id="29" default="reset"}">
        </td>
    </tr>
</table>
