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
            <div class="outer">
                <div class="inner">
                    {jrPlaylist_util mode="embed_playlist" playlist_id=$_post.playlist_id template="channel_playlist.tpl"}
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>