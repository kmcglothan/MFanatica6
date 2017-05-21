{foreach $_audios as $mod => $_i}
    <a href="{$jamroom_url}/{jrCore_module_url module=$mod}/{$_i.view}">
    <div class="audio-choice">
        <img src="{$_i.icon_url}" width="72" height="72" title="{$_i.alt|jrCore_entity_string}" alt="{$_i.alt|jrCore_entity_string}">
        <br>
        {$_i.title|jrCore_entity_string}
    </div>
    </a>
{/foreach}
<div style="clear:both"></div>
<br>
<a id="audio-close" onclick="$('#create_audio_dropdown').fadeOut(100);">{jrCore_icon icon="close" size="16"}</a>
