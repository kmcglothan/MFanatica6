{if $_item_id != 0}
    {jrSoundCloud_embed item_id=$_item_id auto_play=$autoplay width="100%" height="120"}
{else}
    <iframe width="100%" height="120" frameborder="no" src="https://w.soundcloud.com/player/?url=https://api.soundcloud.com/tracks/{$remote_media_id}&auto_play={$autoplay}&show_artwork=1"></iframe>
{/if}