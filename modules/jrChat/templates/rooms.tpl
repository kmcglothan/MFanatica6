{jrCore_module_url module="jrChat" assign="curl"}
<div id="jrchat-room-box">
    {foreach $_rooms as $_r}
        <div class="jrchat-room-opt">
            <a onclick="jrChat_load_room_id('{$_r.room_id}')">{$_r.room_title}</a>
            <div class="jrchat-room-dl"><a href="{$jamroom_url}/{$curl}/transcript/room_id={$_r.room_id}" title="{jrCore_lang module="jrChat" id=35 default="download transcript"}">{jrCore_icon icon="download" size=22}</a></div>
        </div>
    {/foreach}
</div>