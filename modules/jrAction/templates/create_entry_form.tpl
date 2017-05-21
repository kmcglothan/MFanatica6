{jrCore_module_url module="jrAction" assign="murl"}
{jrCore_module_url module="jrImage" assign="iurl"}

{if strlen($editor_html) > 10}

    <style type="text/css">.form_editor_holder { width: 100% !important }</style>

{else}

    <link rel="stylesheet" href="{$jamroom_url}/modules/jrAction/contrib/mentions/jquery.mentionsInput.css?v={$version}" type="text/css">
    <script type="text/javascript" src="{$jamroom_url}/modules/jrAction/contrib/underscore/underscore-min.js?v={$version}"></script>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrAction/contrib/mentions/jquery.mentionsInput.js?v={$version}"></script>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrAction/contrib/mentions/lib/jquery.elastic.js?v={$version}"></script>
    <script type="text/javascript" src="{$jamroom_url}/modules/jrAction/contrib/mentions/lib/jquery.events.input.js?v={$version}"></script>
    <script type="text/javascript">
        $(document).ready(function()
        {
            $('#action_update').mentionsInput({
                onDataRequest: function(mode, query, callback)
                {
                    var d = 'q=' + query;
                    $.getJSON('{$jamroom_url}/{$murl}/mention_profiles', d, function(r)
                    {
                        r = _.filter(r, function(i)
                        {
                            return i.name.toLowerCase().indexOf(query.toLowerCase()) > -1
                        });
                        callback.call(this, r);
                    });
                }
            });
        });
    </script>

{/if}

{jrCore_lang module="jrAction" id=3 default="Post a new Activity Update" assign="ph"}
{jrCore_lang module="jrAction" id=5 default="save update" assign="su"}

<div id="quick_action_box">

    {if $_conf.jrAction_quick_share == 'on' && count($_tabs) > 1 && $quick_share == 1}
    <div id="quick_action_tab_box">
        {foreach $_tabs as $_t}
            {if $_t.module == 'jrAction'}
                <div class="quick_action_tab quick_action_tab_active" title="{$_t.title|jrCore_entity_string}" onclick="jrAction_quick_share(this, '{$_t.module}','{$_t.function}')">{jrCore_icon icon=$_t.icon size=30 class="sprite_icon_hilighted"}</div>
            {else}
                <div class="quick_action_tab" title="{$_t.title|jrCore_entity_string}" onclick="jrAction_quick_share(this, '{$_t.module}','{$_t.function}')">{jrCore_icon icon=$_t.icon size=30}</div>
            {/if}
        {/foreach}
        <div id="quick_action_title">{$ph}</div>
    </div>
    <div style="clear:both"></div>
    {/if}

    <form id="action_form" method="post" action="{$jamroom_url}/{$murl}/create_save" onsubmit="jrAction_submit();return false">
        <input type="hidden" name="jr_html_form_token" value="{$token}">
        <input id="jrAction_function" type="hidden" name="jrAction_function" value="jrAction_quick_share_status_update">

        <div id="quick_action_form">
            {* registered module forms will appear here and hide #quick_action_default_form *}
        </div>

        <div id="quick_action_default_form">
        {if strlen($editor_html) > 10}

            {$editor_html}

        {else}

            <textarea cols="72" rows="6" id="action_update" class="form_textarea" name="action_text" placeholder="{$ph|jrCore_entity_string}"></textarea>

        {/if}
        </div>

        <img id="asi" src="{$jamroom_url}/{$iurl}/img/skin/{$_conf.jrCore_active_skin}/form_spinner.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id=73 default="working..."}" style="display:none">
        <input id="action_submit" type="button" class="form_button" value="{$su|jrCore_entity_string}" onclick="$('#action_form').submit();">

    </form>
</div>
