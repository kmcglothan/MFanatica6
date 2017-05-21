<tr>
    <td>
        <div id="content" style="margin:0;padding:0">
            {if count($tabs) > 0}
                <table class="page_content" style="margin:0">
                    <tr>
                        <td class="page_tab_bar_holder" colspan="2">
                            <ul class="page_tab_bar">
                                {foreach $tabs as $k => $t}
                                    {if $t.module == $default_module}
                                        <li id="t{$t.module}" class="page_tab page_tab_active{if $t@first} page_tab_active page_tab_first{/if}{if $t@last} page_tab_last{/if}" onclick="jrEmbed_load_module('{$t.module}', 1, '');">
                                            <a name="{$t.name}">{$t.name}</a></li>
                                    {else}
                                        <li id="t{$t.module}" class="page_tab{if $t@first} page_tab_active page_tab_first{/if}{if $t@last} page_tab_last{/if}" onclick="jrEmbed_load_module('{$t.module}', 1, '');">
                                            <a name="{$t.name}">{$t.name}</a></li>
                                    {/if}
                                {/foreach}
                            </ul>
                        </td>
                    </tr>
                </table>
                <div class="p10">

                    <div id="embed_spinner" style="margin-left:16px">
                        {jrCore_module_url module="jrImage" assign="url"}
                        <img src="{$jamroom_url}/{$url}/img/skin/{$_conf.jrCore_active_skin}/submit.gif" width="24" height="24" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">
                    </div>

                    <div id="embed_panel" class="panel current">
                        {* Selected tab contents will load here *}
                    </div>

                </div>
            {else}
                <div class="item error p20 center" style="margin:12px 32px">
                    There are no media modules installed for the Editor Embedded Media module to use!<br><br>
                    Make sure you have at least one media module (Audio Support, Video Support, YouTube Support, etc.)<br>
                    installed and active in your Profile Quotas.
                </div>
            {/if}

            <div id="jrembed_amod" style="display:none">{$default_module}</div>
        </div>
    </td>
</tr>
