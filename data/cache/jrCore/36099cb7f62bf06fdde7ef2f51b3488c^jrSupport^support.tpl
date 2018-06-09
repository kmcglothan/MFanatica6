<div style="overflow: hidden">
{if isset($module)}
    <script>
        $(document).ready(function() {
            jrSupport_view_options('module', '{$module}');
        });
    </script>
    <div id="info_box">
        <div class="item" style="display:table;width:100%;margin:0">
            <div style="display:table-row">
                <div style="display:table-cell;width:10%;text-align:center">
                    {$icon}
                </div>
                <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                    <h2>Module Questions</h2>
                    <ul>
                        <li>Have a question about how a module works?</li>
                        <li>Need help configuring a module to suit your needs?</li>
                        <li>Encountered an issue with a module and need help?</li>
                    </ul>
                    <img id="module_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="display:none;margin:2px 0 7px 6px">
                </div>
            </div>
        </div>
    </div>
    <img id="module_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="display:none;margin:2px 0 7px 6px">
    <div id="module_info" style="display:none">{* help results load here *}</div>

{else}

    <script>
        $(document).ready(function() {
            jrSupport_view_options('skin', '{$skin}');
        });
    </script>
    <div id="info_box">
        <div class="item" style="display:table;width:100%;margin:0">
            <div style="display:table-row">
                <div style="display:table-cell;width:10%;text-align:center">
                    {$icon}
                </div>
                <div style="display:table-cell;padding:0 18px;vertical-align:middle;width:90%">
                    <h2>Skin Questions and Customization</h2>
                    <ul>
                        <li>Need help designing or customizing the skin templates?</li>
                        <li>Have questions about a skin configuration?</li>
                        <li>Encountered an issue and need help?</li>
                    </ul>
                    <img id="skin_submit_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}" style="display:none;margin:2px 0 7px 6px">
                </div>
            </div>
        </div>
    </div>
    <div id="skin_info">{* help results load here *}</div>
{/if}
</div>
