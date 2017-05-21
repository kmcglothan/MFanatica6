{jrCore_module_url module="jrSiteBuilder" assign="murl"}
{jrCore_include template="header.tpl"}

{* code editor *}
<link rel="stylesheet" href="{$jamroom_url}/modules/jrCore/contrib/codemirror/lib/codemirror.css" media="screen"/>
<script type="text/javascript" src="{$jamroom_url}/modules/jrCore/contrib/codemirror/lib/codemirror.js"></script>
<script type="text/javascript" src="{$jamroom_url}/modules/jrCore/contrib/codemirror/mode/smarty/smarty.js"></script>

<h1>Test Template Builder</h1>

<br/>

<select name="module" id="module" class="form_select" style="width: auto;" onchange="jrCore_window_location('{$jamroom_url}/{$murl}/template_builder/'+ $(this).val());">
    {foreach $_modules as $name => $title}
        {if isset($mod) && $mod == $name}
            <option value="{$name}" selected="selected">{$title}</option>
        {else}
            <option value="{$name}">{$title}</option>
        {/if}
    {/foreach}
</select>
<select name="template" id="template" class="form_select" style="width: auto;" onchange="jrCore_window_location('{$jamroom_url}/{$murl}/template_builder/'+ $('#module').val() + '/tpl='+ $(this).val());">
    <option value="item_list.tpl">default</option>
    {foreach $_templates as $_t}
        {if isset($_post.tpl) && $_post.tpl == $_t.template_name}
            <option value="{$_t.template_name}" selected="selected">{$_t.template_name}</option>
        {else}
            <option value="{$_t.template_name}">{$_t.template_name}</option>
        {/if}
    {/foreach}
</select>
<br/>

<div id="preview"><!-- display loads here --></div>
<div id="messages" class="page_notice"><!-- save messages load here --></div>


<br/>
<i>Construct a template</i><br>
<button class="form_button" onclick="jrSiteBuilder_preview_template();">Preview</button>
<button class="form_button" onclick="jrSiteBuilder_save_template()">Save</button>
<img id="save_form_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="working..." style="display:none">
<input type="text" name="description" id="description" class="form_text" placeholder="Enter a unique descriptive name for this template" value="{$_post.tpl}"/>
<input type="hidden" name="module" id="module" value="{$mod}"/>
<textarea name="editor" id="editor" cols="30" rows="10" style="width: 100%;">{$tpl}</textarea>
<br/>
<br/>
<div id="tabs">
    <table class="page_content">
        <tr>
            <td class="page_tab_bar_holder" colspan="2">
                <ul class="page_tab_bar">
                    <li id="t{$t.module}" class="page_tab page_tab_active page_tab_active page_tab_first{if $t@last} page_tab_last{/if}"><a name="#" style="text-transform: lowercase;">$_conf</a></li>
                    <li id="t{$t.module}" class="page_tab"><a name="#" style="text-transform: lowercase;">Items</a></li>
                    <li id="t{$t.module}" class="page_tab"><a name="#" style="text-transform: lowercase;">Structure</a></li>
                    <li id="t{$t.module}" class="page_tab"><a name="#" style="text-transform: lowercase;">$_post</a></li>
                    <li id="t{$t.module}" class="page_tab page_tab_last"><a name="#" style="text-transform: lowercase;">Delete</a></li>
                </ul>
            </td>
        </tr>
    </table>
</div>

{* the changing div *}
<div style="height: 600px; overflow: auto;" id="options">
    <p>The <b>$_conf</b> variable is an array that contains information that is configured site wide.
        It usually comes from the GLOBAL CONFIG tab of modules and relates to site wide setting for the module.
        It does not usually change depending on who is looking at the screen or what quota the user is in.
    </p>

    <table>
        <tr>
            <th colspan="2">Config ($_conf)</th>
        </tr>
        {foreach $_conf as $k => $v}
            <tr>
                <td>{ldelim}$_conf.{$k}{rdelim}</td>
                <td>{$v}</td>
            </tr>
        {/foreach}
    </table>
</div>

<script type="text/javascript">
    var cm;
    $(document).ready(function() {
        cm = CodeMirror.fromTextArea(document.getElementById("editor"), {
            mode: "smarty",
            lineNumbers: true,
            smartyVersion: 3
        });
        cm.setSize(1128, 400);
//        cm.on('change', function()
//        {
//            var html = cm.getValue();
//            $('#list_custom_template').html(html);
//        });

    });
</script>
{jrCore_include template="footer.tpl"}
