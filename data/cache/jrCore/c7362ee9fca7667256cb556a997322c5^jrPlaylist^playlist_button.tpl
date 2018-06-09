{*the button that displays 'add to playlist' and 'add a playlist' *}
<div style="display: inline-block;" id="playlist_button_{$playlist_for}_{$item_id}">
    {jrCore_lang module="jrPlaylist" id="2" default="add to playlist" assign="alt"}
    {$icon_html}
    <div id="playlist_{$playlist_for}_{$item_id}" class="overlay playlist_box"><!-- playlist loads here --></div>
</div>