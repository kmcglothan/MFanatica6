<script type="text/javascript">
    $(document).ready(function() {


        var jp_volume = 0.8;

        var autoPlayCookie = jrReadCookie('jrMogul_video_autoplay');
        var volumeCookie = jrReadCookie('jrMogul_audio_volume');
        var muteCookie = jrReadCookie('jrMogul_auto_mute');

        if(volumeCookie && volumeCookie.length > 0) {
            jp_volume = volumeCookie;
        }

        if (autoPlayCookie && autoPlayCookie == 1) {
            $('#jp_container_{$uniqid} #jp-auto-play').prop('checked', true);
            var autoPlay = true;
        }


        var tw = $('#jp_container_{$uniqid}').width();
        var th = Math.round(tw / 1.778);
        $('#jp_container_{$uniqid} .jp-gui').height(th-30);

        var playlist_{$uniqid} = new jPlayerPlaylist({
            jPlayer:"#jquery_jplayer_{$uniqid}",
            cssSelectorAncestor:"#jp_container_{$uniqid}"
        },[
            {if isset($media) && is_array($media)}
            {jrCore_module_url module="jrVideo" assign="vurl"}
            {foreach $media as $v}
            {
                title: "{$v._item.video_title|htmlentities|truncate:55}",
                artist: "{$v._item.profile_name|htmlentities|truncate:55}",
                artist_url: "{$jamroom_url}/{$v._item.profile_url}",
                album_url: "{$jamroom_url}/{$v._item.profile_url}/{$vurl}/albums/{$v._item.video_album_url}",
                item_url: "{$jamroom_url}/{$v._item.profile_url}/{$v.module_url}/{$v._item_id}/{$v._item.video_album_url}",
                poster: "{$v.poster}?crop=" + tw +':'+ th,
                image: "{$jamroom_url}/{$vurl}/image/video_image/{$v.item_id}/24/crop=auto",
                "{$v._item.video_file_extension}": "{$jamroom_url}/{$vurl}/stream/video_file/{$v.item_id}/key=[jrCore_media_play_key]/file.{$v._item.video_file_extension}",
                {if strstr($formats, 'm4v')}
                m4v: "{$jamroom_url}/{$vurl}/stream/video_file/{$v.item_id}/key=[jrCore_media_play_key]/file.m4v",
                {/if}
                id: "{$v.item_id}",
                profile_id: "{$v._item.profile_id}",
                module: "{$v.module}",
                field: 'video_file',
                price : {$v._item.video_file_item_price|default:"0"},
                url: "{$v.module_url}",
                prefix : "video"
            },
            {/foreach}
            {/if}
        ],{
            error: function(res) { jrCore_stream_url_error(res); },
            playlistOptions: {
                autoPlay: autoPlay || {$autoplay}
            },
            ready: function(){
                if ((jp_volume && jp_volume == 0) || (muteCookie && muteCookie == 1)) {
                    $(this).jPlayer('volume', 0.5);
                    $(this).jPlayer('mute');
                }

                if (muteCookie && muteCookie == 1) {
                    $('#jp_container_{$uniqid} #jp-auto-mute').prop('checked', true);
                    vBar.hide();
                }
            },
            ended : function(){
                $('#jp_container_{$uniqid} .jp-gui').addClass('start');
                $('#jp_container_{$uniqid} .jp-video-play').show();
                $('#jp_container_{$uniqid} .jp-video-play-icon').addClass('replay').show();
                $('#jp_container_{$uniqid} .jp-play').addClass('replay');
            },
            play : function(){
                gui.removeClass('start');
                $('#jp_container_{$uniqid} .jp-play').removeClass('replay');
                $('#jp_container_{$uniqid} .jp-video-play-icon').removeClass('replay').hide();
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            smoothPlayBar: true,
            keyEnabled: true,
            volume: jp_volume,
            preload:'none',
            mode: 'window',
            remainingDuration: true,
            toggleDuration: true,
            {if jrCore_is_mobile_device()}
            autohide: { hold: 2500 },
            {else}
            autohide: { restored: true, fadein: 50, hold: 2500 },
            {/if}
            size: { width: "100%", height: th + "px" }
        });

        var holder = $('#jp_container_{$uniqid} .jp-controls-holder');
        var gui = $('#jp_container_{$uniqid}  .jp-gui');
        var settings = $('#jp_container_{$uniqid}  .jp-settings');
        var controls = $('#jp_container_{$uniqid}  .jp-control-settings');
        var vBar = $('#jp_container_{$uniqid}  .jp-volume-bar');
        var list = $('#jp_container_{$uniqid}  .jp-playlist');
        var title = $('#jp_container_{$uniqid}  .jp-title');


        settings.mouseover(function(){
            controls.show();
            vBar.hide();
        });
        $('#jp_container_{$uniqid}  .jp-interface').mouseleave(function(){
            controls.hide();
            vBar.hide();
        });

        $('#jp_container_{$uniqid}  .jp-mute, #jp_container_{$uniqid}  .jp-unmute').mouseover(
            function (){
                vBar.show();
                controls.hide();
            }
        );
        $('#jp_container_{$uniqid}  input#jp-show').click(function() {
            if (!$(this).is(':checked')) {
                list.hide();
                title.css({
                    visibility : "visible"
                });
            }
            else {
                list.show();
                title.css({
                    visibility : "collapse"
                });
            }
        });

        vBar.hide();

        $('#jp_container_{$uniqid}').bind($.jPlayer.event.volumechange, function(event){
            jp_volume = event.jPlayer.options.volume;
            if($("#jp_container_{$uniqid}").hasClass('jp-state-muted')) {
                jp_volume = 0
            }
            jrSetCookie('jrMogul_audio_volume', jp_volume, 31);
        });

        $('#jp_container_{$uniqid} #jp-auto-play').click(function() {
            jrSetCookie('jrMogul_video_autoplay', 0, 31);

            if ($(this).is(':checked')) {
                jrSetCookie('jrMogul_video_autoplay', 1, 31);
            }
        });
        $('#jp_container_{$uniqid} #jp-auto-mute').click(function() {
            jrSetCookie('jrMogul_auto_mute', 0, 31);
            if ($(this).is(':checked')) {
                jrSetCookie('jrMogul_auto_mute', 1, 31);
            }
        });
    });
</script>