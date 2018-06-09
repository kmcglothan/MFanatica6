<script type="text/javascript">
    $(document).ready(function()
    {
        var tw = $('#jp_container_{$uniqid}').width();
        var th = 400;

        new jPlayerPlaylist({
            jPlayer:"#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor:"#jp_container_{$uniqid}"
        },[
            {if isset($media) && is_array($media)}
            {jrCore_module_url module="jrVideo" assign="vurl"}
            {foreach $media as $v}
            {
                title: "{$v._item.video_title|htmlentities}",
                artist: "{$v._item.profile_name|htmlentities}",
                artist_url: "{$jamroom_url}/{$v._item.profile_url}",
                album: "{$v._item.video_album|htmlentities}",
                album_url: "{$jamroom_url}/{$v._item.profile_url}/{$vurl}/albums/{$v._item.video_album_url}",
                {foreach $v.formats as $fext => $furl}
                    "{$fext}": "{$furl}",
                {/foreach}
                poster: "{$v.poster}/crop=" + tw +':'+ th
            },
            {/foreach}
            {/if}
        ],{
            error: function(r) {
                jrCore_stream_url_error(r);
            },
            playlistOptions: {
                autoPlay: {$autoplay}
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            smoothPlayBar: true,
            keyEnabled: true,
            volume: 0.8,
            preload: 'none',
            mode: 'window',
            {if jrCore_is_mobile_device()}
            autohide: { hold: 2500 },
            {else}
            autohide: { restored: true, fadein: 50, hold: 2500 },
            {/if}
            size: { width: tw + "px", height: th + "px" }
        });
        setTimeout(function()
        {
            $("#jp_container_{$uniqid}").parent().fadeTo(300, 1);
        }, 200);
    });
</script>