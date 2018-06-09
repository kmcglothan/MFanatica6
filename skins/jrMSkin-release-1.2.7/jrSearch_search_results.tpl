{jrCore_module_url module="jrSearch" assign="murl"}
<div class="container search_results_container">
    <div class="row">
        <div class="col12 last">
            <div class="box">
                {jrMSkin_sort template="icons.tpl" nav_mode="jrSearch"}
                <div class="box_body">
                    <div class="wrap">
                        <div class="media">
                            <div class="wrap">
                                {if $module_count == 1}
                                    <div class="breadcrumbs" style="padding: 8px 0 0;">
                                        <a href="{$jamroom_url}">{jrCore_lang module="jrSearch" id="11" default="Home"}</a>
                                        <a href="{$jamroom_url}/{$murl}/results/all/1/{$_conf.jrSearch_index_limit|default:4}/search_string={$search_string}">{jrCore_lang module="jrSearch" id="6" default="All Search Results for"}
                                            &quot;{$search_string|replace:'&quot;':''}&quot;</a>
                                        <a href="#">{$titles[$modules]}</a>
                                    </div>
                                    <span class="title" style="clear: both;">&quot;{$search_string|replace:"&quot;":''}
                                        &quot; in {$titles[$modules]}</span>
                                {else}
                                    <span class="title"
                                          style="clear: both;">{jrCore_lang module="jrSearch" id="8" default="Search Results for"}
                                        &quot;{$search_string|replace:"&quot;":''}&quot;</span>
                                {/if}

                                <form method="get"
                                      action="{$jamroom_url}/{$murl}/results/{$modules}/{$page}/{$pagebreak}"
                                      target="_self"
                                      onsubmit="$('#form_submit_indicator').show(300, function() { return true } );">
                                    <input type="text" name="search_string" class="form_text" value="{$search_string}"
                                           style="max-width: 240px;">
                                    <span style="display:inline-block;margin-top:8px;"><img id="form_submit_indicator"
                                                                                            src="{$jamroom_url}/skins/jrMSkin/img/submit.gif"
                                                                                            width="24" height="24"
                                                                                            alt="{jrCore_lang module="jrCore" id="73" default="working..."}"><input
                                                type="submit" class="form_button"
                                                value="{jrCore_lang module="jrSearch" id="7" default="search"} {$titles[$modules]}"></span>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

        {if count($results) > 0}

        {foreach $results as $module => $result}

        {if $module_count > 1}
        {if $result@iteration % 2 === 1}
        <div class="row">
            <div class="col6">
                <div class="box" style="margin: 0 5px 1em">
                    {else}
                    <div class="col6 last">
                        <div class="box" style="margin: 0 5px 1em">
                            {/if}
                            {else}
                            <div class="row">
                                <div class="col12 last">
                                    <div class="box">
                                        {/if}

                                        {jrMSkin_sort template="icons.tpl" nav_mode=$module}

                                        <div class="box_body">
                                            <div class="wrap">
                                                <div id="list">
                                                    {$result}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {if $result@iteration % 2 === 0 || $module_count == '1' || $result@last === true}
                            </div>
                            {/if}

                            {/foreach}

                            {* prev/next page profile footer links *}
                            {if $module_count == 1}
                                {if $info[$module].prev_page > 0 || $info[$module].next_page > 0}
                                    <div class="block">
                                        <table style="width:100%">
                                            <tr>
                                                <td style="width:25%">
                                                    {if $info[$module].prev_page > 0}
                                                        <a href="{$jamroom_url}/{$murl}/results/{$module}/{$info[$module].prev_page}/{$pagebreak}/search_string={$search_string}">{jrCore_icon icon="previous"}</a>
                                                    {/if}
                                                </td>
                                                <td style="width:50%;text-align:center">
                                                    <form name="form" method="post" action="_self">
                                                        <select name="pagenum" class="form_select list_pager"
                                                                style="width:60px;"
                                                                onchange="window.location='{$jamroom_url}/{$murl}/results/{$module}/' + $(this).val() +'/{$pagebreak}/search_string={$search_string}'; ">
                                                            {for $pages=1 to $info[$module].total_pages}
                                                                {if $info[$module].page == $pages}
                                                                    <option value="{$info[$module].this_page}"
                                                                            selected="selected"> {$info[$module].this_page}</option>
                                                                {else}
                                                                    <option value="{$pages}"> {$pages}</option>
                                                                {/if}
                                                            {/for}
                                                        </select>&nbsp;/&nbsp;{$info[$module].total_pages}
                                                    </form>
                                                </td>
                                                <td style="width:25%;text-align:right">
                                                    {if $info[$module].next_page > 0}
                                                        <a href="{$jamroom_url}/{$murl}/results/{$module}/{$info[$module].next_page}/{$pagebreak}/search_string={$search_string}">{jrCore_icon icon="next"}</a>
                                                    {/if}
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                {/if}
                            {/if}

                            {else}

                            <div class="row">
                                <div class="col12 last">
                                    <div class="page_note" style="margin-bottom:12px">
                                        {jrCore_lang module="jrSearch" id="10" default="No results found for your search"}
                                    </div>
                                </div>
                            </div>

                            {/if}

                        </div>

