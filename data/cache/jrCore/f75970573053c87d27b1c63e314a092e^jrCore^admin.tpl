{jrCore_module_url module="jrCore" assign="core_url"}

<div id="admin_container" class="container">
    <div class="row">

        <div class="col3">
            <div class="item-list">

                <table>
                    <tr>
                        <td class="page_tab_bar_holder">
                            <ul class="page_tab_bar">
                                <li id="dtab" class="page_tab page_tab_first"><a href="{$jamroom_url}/{$core_url}/dashboard">dashboard</a></li>
                                {if isset($active_tab) && $active_tab == 'skins'}
                                    <li id="mtab" class="page_tab"><a href="{$jamroom_url}/{$core_url}/admin/global">modules</a></li>
                                    <li id="stab" class="page_tab page_tab_last page_tab_active"><a href="{$jamroom_url}/{$core_url}/skin_admin">skins</a></li>
                                {else}
                                    <li id="mtab" class="page_tab page_tab_active"><a href="{$jamroom_url}/{$core_url}/admin/global">modules</a></li>
                                    <li id="stab" class="page_tab page_tab_last"><a href="{$jamroom_url}/{$core_url}/skin_admin">skins</a></li>
                                {/if}
                            </ul>
                        </td>
                    </tr>
                </table>

                <div id="item-holder">
                    <dl class="accordion">

                    {if isset($_modules)}

                        {* MODULES *}
                        <dt class="page_section_header admin_section_header admin_section_search">
                            <input type="text" name="ss" class="form_text form_admin_search" placeholder="Search" onkeypress="if (event && event.keyCode === 13 && this.value.length > 2) { jrCore_window_location('{$jamroom_url}/{$core_url}/search/ss='+ jrE(this.value));return false; };">
                        </dt>

                        {foreach $_modules as $category => $_mods}

                            <a href="" class="accordion_section_{jrCore_url_string($category)}"><dt class="page_section_header admin_section_header">{$category}</dt></a>
                            {if $category == $default_category}
                            <dd id="c{$category}">
                            {else}
                            <dd id="c{$category}" style="display:none">
                            {/if}

                                {foreach $_mods as $mod_dir => $_mod}
                                    {jrCore_get_module_index module=$mod_dir assign="url"}
                                    <a href="{$jamroom_url}/{$_mod.module_url}/{$url}" class="tt{$mod_dir}">
                                    {if isset($_post.module) && $_post.module == $mod_dir}
                                        <div class="item-row item-row-active">
                                    {else}
                                        <div class="item-row">
                                    {/if}
                                        <div class="item-icon">
                                            {jrCore_get_module_icon_html module=$mod_dir size=32}
                                        </div>
                                        <div class="item-entry">{$_mod.module_name}</div>
                                        <div class="item-enabled">
                                        {if $_mod.module_active != '1'}
                                            <span class="item-disabled" title="module is currently disabled">D</span>
                                        {/if}
                                        </div>
                                    </div>
                                    </a>
                                {/foreach}

                            </dd>
                        {/foreach}

                    {else}

                        {* SKINS *}
                        <dt class="page_section_header admin_section_header admin_section_search">
                            <input type="text" name="ss" class="form_text form_admin_search" placeholder="Search" onkeypress="if (event && event.keyCode === 13 && this.value.length > 2) { jrCore_window_location('{$jamroom_url}/{$core_url}/search/sa=skin/skin={$_conf.jrCore_active_skin}/ss='+ jrE(this.value));return false; };">
                        </dt>

                        {foreach $_skins as $category => $_skns}

                            <a href="" class="accordion_section_{jrCore_url_string($category)}"><dt class="page_section_header admin_section_header">{$category}</dt></a>
                            {if $category == $default_category}
                            <dd id="c{$category}">
                            {else}
                            <dd id="c{$category}" style="display:none">
                            {/if}

                                {foreach $_skns as $skin_dir => $_skin}
                                    <a href="{$jamroom_url}/{$core_url}/skin_admin/info/skin={$skin_dir}" class="tt{$skin_dir}">
                                    {if (isset($_post.skin) && $_post.skin == $skin_dir) || (!isset($_post.skin) && $skin_dir == $_conf.jrCore_active_skin) }
                                        <div class="item-row item-row-active">
                                    {else}
                                        <div class="item-row">
                                    {/if}
                                        <div class="item-icon">
                                            {jrCore_get_skin_icon_html skin=$skin_dir size=32}
                                        </div>
                                        <div class="item-entry">{$_skin.title}</div>
                                    </div>
                                    </a>
                                {/foreach}

                            </dd>
                        {/foreach}

                    {/if}

                </div>
            </div>
        </div>

        <div class="col9 last">
            <div id="item-work">
                {$admin_page_content}
            </div>
        </div>

    </div>
</div>
