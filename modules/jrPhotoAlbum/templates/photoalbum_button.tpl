{*the button that displays 'add to photo album' and 'add a photo album' *}
<div style="display: inline-block;" id="photoalbum_button_{$photoalbum_for}_{$item_id}">
    {jrCore_lang module="jrPhotoAlbum" id=9 default="add to photo album" assign="alt"}
    {$icon_html}
    <div id="photoalbum_{$photoalbum_for}_{$item_id}" class="overlay photoalbum_box"><!-- photo album loads here --></div>
</div>