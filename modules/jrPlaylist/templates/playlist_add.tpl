{*this is the list of all the playlists the user currently has and a box to add a new one *}
<div id="playlist_message" style="display:none"></div>
<form id="new_playlist_form">
<table>

    {if is_array($_items)}
        {jrCore_module_url module="jrPlaylist" assign="murl"}
        {foreach $_items as $_p}
        <tr>
            <td class="playlist_name">
            {if jrUser_is_logged_in()}
                <a href="{$jamroom_url}/{$_p.profile_url}/{$murl}/{$_p._item_id}/{$_p.playlist_title_url}">{$_p.playlist_title|truncate:30}</a>
            {else}
                <a href="{$jamroom_url}/{$murl}/view/{$_p.playlist_title_url}">{$_p.playlist_title|truncate:30}</a>
            {/if}
            </td>
            <td class="playlist_count">{$_p.playlist_count} {jrCore_lang module="jrPlaylist" id="10" default="tracks"}</td>
            <td><input type="button" class="form_button playlist_button" value="{jrCore_lang module="jrPlaylist" id="16" default="add"}" onclick="jrPlaylist_inject('{$_p._item_id}','{$item_id}','{$playlist_for}')" style="margin:0 2px 5px 0;"></td>
        </tr>
        {/foreach}

        {* page jumper *}
        {if $info.total_pages > 1}
            <tr>
                <td colspan="2">
                    {if $info.this_page > 1}
                        <a href="" onclick="jrPlaylist_select('{$item_id}','{$playlist_for}','{$info.prev_page}');return false">{jrCore_icon icon="arrow-left" size="16"}</a>
                    {/if}
                </td>
                <td colspan="2" style="text-align:right;padding:3px;">
                    {if $info.next_page > 0}
                        <a href="" onclick="jrPlaylist_select('{$item_id}','{$playlist_for}','{$info.next_page}');return false">{jrCore_icon icon="arrow-right" size="16"}</a>
                    {/if}
                </td>
            </tr>
        {/if}

    {/if}

    <tr>
        <td colspan="2" style="width:95%;padding-top:12px;">
            <input id="new_playlist_{$item_id}" type="text" class="form_text" style="width:90%;" value="{jrCore_lang module="jrPlaylist" id="17" default="new playlist name"}" name="new_playlist" onfocus="if (this.value == '{jrCore_lang module="jrPlaylist" id="17" default="new playlist name"}'){ this.value = ''; }" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) {ldelim} $('#new_playlist_form').submit(function(event) { event.stopPropagation()}); {rdelim}">
        </td>
        <td style="width:5%;padding-top:12px;">
            <input type="submit" value="{jrCore_lang module="jrPlaylist" id="18" default="create"}" class="form_button playlist_button" onclick="jrPlaylist_new('{$item_id}','{$playlist_for}');return false;" style="margin:0 2px 0 0;">
        </td>
    </tr>

</table>
</form>

<div style="float:right;clear:both;margin:3px;padding-top:3px;">
    <img id="playlist_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="16" height="16" style="display:none" alt="{jrCore_lang module="jrCore" id="73" default="working..."}">&nbsp;
    <a id="playlist_close" href="" onclick="jrPlaylist_hide();return false">{jrCore_icon icon="close" size="16"}</a>
</div>
