{*this is the list of all the photo albums the user currently has and a box to add a new one *}
<form id="new_photoalbum_form">
    <table>

        {if is_array($_items)}
            {jrCore_module_url module="jrPhotoAlbum" assign="murl"}
            {foreach $_items as $_p}
                <tr>
                    <td class="photoalbum_name">
                        {if jrUser_is_logged_in()}
                            <a href="{$jamroom_url}/{$_p.profile_url}/{$murl}/{$_p._item_id}/{$_p.photoalbum_title_url}">{$_p.photoalbum_title|truncate:30}</a>
                        {else}
                            <a href="{$jamroom_url}/{$murl}/view/{$_p.photoalbum_title_url}">{$_p.photoalbum_title|truncate:30}</a>
                        {/if}
                    </td>
                    <td class="photoalbum_count">{$_p.photoalbum_count} {jrCore_lang module="jrPhotoAlbum" id="14" default="photos"}</td>
                    <td><input type="button" class="form_button photoalbum_button" value="{jrCore_lang module="jrPhotoAlbum" id=15 default="add"}" onclick="jrPhotoAlbum_inject('{$_p._item_id}','{$item_id}','{$photoalbum_for}')" style="margin:0 2px 5px 0;"></td>
                </tr>
            {/foreach}

            {* page jumper *}
            {if $info.total_pages > 1}
                <tr>
                    <td colspan="2">
                        {if $info.this_page > 1}
                            <a onclick="jrPhotoAlbum_select('{$item_id}','{$photoalbum_for}','{$info.prev_page}');return false">{jrCore_icon icon="arrow-left" size="16"}</a>
                        {/if}
                    </td>
                    <td colspan="2" class="p5 right">
                        {if $info.next_page > 0}
                            <a onclick="jrPhotoAlbum_select('{$item_id}','{$photoalbum_for}','{$info.next_page}');return false">{jrCore_icon icon="arrow-right" size="16"}</a>
                        {/if}
                    </td>
                </tr>
            {/if}

        {/if}

        <tr>
            <td style="padding-top:12px;">
                <input id="new_photoalbum_{$item_id}" type="text" class="form_text" style="width:90%;" placeholder="{jrCore_lang module="jrPhotoAlbum" id=16 default="new photo album name"}" name="new_photoalbum" onkeypress="if (event && event.keyCode == 13 && this.value.length > 0) {ldelim} $('#new_photoalbum_form').submit(function(event) { event.stopPropagation()}); {rdelim}">
            </td>
            <td class="center" style="padding-top:12px;">
                <img id="photoalbum_indicator" src="{$jamroom_url}/skins/{$_conf.jrCore_active_skin}/img/submit.gif" width="16" height="16" style="display:none" alt="{jrCore_lang module="jrCore" id=73 default="working..."}">
            </td>
            <td style="padding-top:12px;">
                <input type="submit" value="{jrCore_lang module="jrPhotoAlbum" id=17 default="create"}" class="form_button photoalbum_button" onclick="jrPhotoAlbum_new('{$item_id}','{$photoalbum_for}');return false">
            </td>
        </tr>

        <tr>
            <td colspan="2" style="padding:0">
                <div id="photoalbum_message" style="display:none"></div>
            </td>
            <td style="width:5%" class="p5 right">
                <a id="photoalbum_close" href="" onclick="jrPhotoAlbum_hide();return false">{jrCore_icon icon="close" size="16"}</a>
            </td>
        </tr>

    </table>
</form>
