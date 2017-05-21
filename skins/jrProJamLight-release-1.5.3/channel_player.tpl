{jrCore_include template="meta.tpl"}
<body>
<style type="text/css">
    .sb-button {ldelim}
        display: none;
    {rdelim}
</style>

<div class="container">
    <div class="row">
        <div class="col12 last">
            {if isset($_post.playlist_id)}
                {jrPlaylist_util mode="embed_playlist" playlist_id=$_post.playlist_id template="channel_playlist.tpl"}
            {else}
                {capture name="row_template" assign="video_player_row"}
                    {literal}
                        {if isset($_items)}
                        {if isset($_conf.jrProJamLight_auto_play) && $_conf.jrProJamLight_auto_play == 'on'}
                        {assign var="vap" value="true"}
                        {elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
                        {assign var="vap" value="true"}
                        {else}
                        {assign var="vap" value="false"}
                        {/if}

                        {foreach from=$_items item="item"}
                        {assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
                        {assign var="player_type" value=$_conf.$skin_player_type}
                        {assign var="player" value="jrVideo_`$player_type`"}
                        {if isset($player_type) && strlen($player_type) > 0}
                            {jrCore_media_player type=$player module="jrVideo" field="video_file" item=$item autoplay=$vap}
                        {else}
                            {jrCore_media_player module="jrVideo" field="video_file" item=$item autoplay=$vap}
                        {/if}
                        {/foreach}
                        {/if}
                    {/literal}
                {/capture}
                {jrCore_list module="jrVideo" order_by="_item_id asc" limit="1" search1="_item_id = `$option`" template=$video_player_row}
            {/if}
        </div>
    </div>
</div>

</body>
</html>