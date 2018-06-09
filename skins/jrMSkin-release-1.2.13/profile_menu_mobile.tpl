{if isset($_items)}
    <div class="menu_select">
        <select name="pchoice" class="form_select" id="profile_select_menu" onchange="var v=this.options[this.selectedIndex].value;jrCore_window_location(v);">
            <option value="{$jamroom_url}/{$profile_url}" selected="selected">{jrCore_lang skin=$_conf.jrCore_active_skin id=1 default="Home"}</option>
            {foreach from=$_items key="module" item="entry"}
                {if $entry.active == '1'}
                    <option value="{$entry.target}" selected="selected"> {$entry.label}</option>
                {else}
                    <option value="{$entry.target}"> {$entry.label}</option>
                {/if}
            {/foreach}
        </select>
    </div>
{/if}

