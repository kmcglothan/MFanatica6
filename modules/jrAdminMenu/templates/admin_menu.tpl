{* menu by http://www.htmldog.com/articles/suckerfish/dropdowns *}
{jrCore_module_url module="jrImage" assign="murl"}
<style type="text/css">
    body { padding-top: 24px; }
</style>

<ul id="adminMenu" class="css-fixed">
    {foreach $_modules as $category => $_m}
        <li><a class="top-row">{$category}</a>
            <ul>
                {* MODULES *}
                {foreach from=$_m key="mod_dir" item="_mod"}
                    {jrCore_get_module_index module=$mod_dir assign="url"}
                    <li><a class="arrow" href="{$jamroom_url}/{$_mod.module_url}/{$url}">{$_mod.module_name}</a>
                        {if is_array($_mod.tabs)}
                        <ul>
                            {* TABS *}
                            {foreach $_mod.tabs as $_tabs}
                                {if $_tabs.label == 'tools' && is_array($_tabs.tools)}
                                    <li><a class="arrow" href="{$_tabs.url}">{$_tabs.label}</a>
                                        <ul>
                                            {* TOOLS *}
                                            {foreach $_tabs.tools as $_tool}
                                                <li><a href="{$_tool.url}">{$_tool.label}</a></li>
                                            {/foreach}
                                        </ul>
                                    </li>
                                {else}
                                    <li><a href="{$_tabs.url}">{$_tabs.label}</a></li>
                                {/if}
                            {/foreach}
                            {/if}
                        </ul>
                    </li>
                {/foreach}

            </ul>
        </li>
    {/foreach}

    <li><a class="top-row">{jrCore_lang module="jrAdminMenu" id="10" default="Skins"}</a>
        <ul>
            {* SKINS *}
            {foreach $_skins as $dir => $skin}
                <li><a href="{$jamroom_url}/core/skin_admin/info/skin={$dir}">{$skin}
                        {* Asterisk the customer facing skin *}
                        {if $customer_facing_skin == $dir} * {/if}
                    </a>
                    <ul>
                        {* TABS *}
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/global/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="1" default="Global Config"}</a>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/style/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="2" default="Style"}</a>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/images/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="3" default="Images"}</a>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/language/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="4" default="Language"}</a>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/templates/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="5" default="Templates"}</a>
                        </li>
                        <li>
                            <a href="{$jamroom_url}/core/skin_admin/info/skin={$dir}">{jrCore_lang module="jrAdminMenu" id="6" default="Info"}</a>
                        </li>
                    </ul>
                </li>
            {/foreach}

        </ul>
    </li>
</ul>

<div style="clear: left"></div>

<script type="text/javascript">
    sfHover = function()
    {
        var s = document.getElementById("nav").getElementsByTagName("LI");
        for (var i = 0; i < s.length; i++) {
            s[i].onmouseover = function()
            {
                this.className += " sfhover";
            };
            s[i].onmouseout = function()
            {
                this.className = this.className.replace(new RegExp(" sfhover\\b"), "");
            }
        }
    };
    if (window.attachEvent) window.attachEvent("onload", sfHover);
</script>