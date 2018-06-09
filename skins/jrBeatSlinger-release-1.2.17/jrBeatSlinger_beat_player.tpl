{jrCore_module_url module=$params.module assign="murl"}

{assign var="ext" value="`$params.field`_extension"}

<script type="text/javascript">

    var playlist_1;

    $(document).ready(function () {

        var jp_volume = 0.8;
        //If there is a cookie and is numeric, get it.

        var volumeCookie = jrReadCookie('n8Beats_audio_volume')

        if (volumeCookie && volumeCookie.length > 0) {
            jp_volume = volumeCookie;
        }

        playlist_1 = new jPlayerPlaylist({
            jPlayer: "#jquery_jplayer_1",
            cssSelectorAncestor: "#jp_container_1"
        }, [
            {if is_array($media)}
            {foreach $media as $a}
            {if $a._item.$ext == 'mp3'}
            {
                title: "{$a.title|truncate:50}",
                artist: "{$a.artist|truncate:50}",
                mp3: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.mp3",
                {if strstr($formats, 'oga')}
                oga: "{$jamroom_url}/{$a.module_url}/stream/{$params.field}/{$a.item_id}/key=[jrCore_media_play_key]/file.ogg",
                {/if}
                poster: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/large/crop=3:2",
                image: "{$jamroom_url}/{$a.module_url}/image/audio_image/{$a.item_id}/24/crop=auto",
                id: "{$a.item_id}",
                profile_id: "{$o.profile_id}",
                album_url: "{$jamroom_url}/{$a._item.profile_url}/{$a.module_url}/albums/{$a._item.audio_album_url}",
                item_url: "{$jamroom_url}/{$a._item.profile_url}/{$a.module_url}/{$a.item_id}/{$a._item.audio_album_url}",
                key: "[jrCore_media_play_key]",
                module: "{$a.module}",
                price: {$a._item.audio_file_item_price|default:"0"},
                album: "{$a._item.audio_album|default:"N/A"}",
                genre: "{$a._item.audio_genre|default:""}",
                date: "{$a._item._created|jrCore_date_format:"relative"}",
                field: 'audio_file',
                currency: "{$_conf.jrFoxyCart_store_currency}",
                url: "{$a.module_url}",
                prefix: "audio"
            },
            {/if}
            {/foreach}
            {/if}
        ], {
            ready: function () {
                if (jp_volume && jp_volume == 0) {
                    $(this).jPlayer('volume', 0.5);
                    $(this).jPlayer('mute');
                }
            },
            play : function () {
                var player = $('.jrBeatSlinger_beat_player');
                if ( !player.hasClass('min') && !player.hasClass('playing')) {
                    player.addClass('playing');
                }

                var song = playlist_1.playlist[playlist_1.current];
                showBeatInfo(song);
            },
            error: function (res) {
                jrCore_stream_url_error(res);
            },
            playlistOptions: {
                autoPlay: false,
                displayTime: 'fast'
            },
            swfPath: "{$jamroom_url}/modules/jrCore/contrib/jplayer",
            supplied: "{$formats}",
            solution: "{$solution}",
            volume: jp_volume,
            smoothPlayBar: true,
            keyEnabled: true,
            preload: 'none',
            mode: 'window',
            toggleDuration: true
        });

        var holder = $('div#jp_container_1 .jp-controls-holder');
        var gui = $('div#jp_container_1  .jp-gui');
        var vBar = $('div#jp_container_1  .jp-volume-bar');
        var list = $('div#jp_container_1  .jp-playlist-bg');
        var title = $('div#jp_container_1  .jp-title2');


        var jp1 =  $('#jp_container_1');
        jp1.mouseleave(function () {
            vBar.hide();
        });

        $('div#jp_container_1  .jp-mute, #jp_container_1  .jp-unmute').mouseover(
                function () {
                    vBar.show();
                }
        );

        $('div#jp_container_1  .jp-show').click(function () {
            list.toggle()
        });

        vBar.hide();

        jp1.bind($.jPlayer.event.volumechange, function (event) {
            jp_volume = event.jPlayer.options.volume;
            if (jp1.hasClass('jp-state-muted')) {
                jp_volume = 0
            }
            //Store the volume in a cookie.
            jrSetCookie('n8Beats_audio_volume', jp_volume, 31);
        });

        $('.index .play a').click(function(){
            $('#jquery_jplayer_1').jPlayer('play');
        });

        $('.jrBeatSlinger_beat_player .jp-stop').click(function(){
            $('.jrBeatSlinger_beat_player').removeClass('playing min');
        });

        $('.jrBeatSlinger_beat_player .jp-min').click(function(){
            $('.jrBeatSlinger_beat_player').toggleClass('min');
        });

        $('.jrBeatSlinger_beat_player .jp-close a').click(function(e){
            e.preventDefault();
            $('.jrBeatSlinger_beat_player .jp-playlist-bg').toggle();
        });

        $('.audio_button').click(function(){
            var song = [];
            song.title = $(this).find('#title').val();
            song.artist = $(this).find('#artist').val();
            song.mp3 = $(this).find('#mp3').val();
            song.oga = $(this).find('#oga').val();
            song.poster = $(this).find('#poster').val();
            song.image = $(this).find('#image').val();
            song.id = $(this).find('#id').val();
            song.profile_id = $(this).find('#profile_id').val();
            song.album_url = $(this).find('#album_url').val();
            song.item_url = $(this).find('#item_url').val();
            song.key = $(this).find('#key').val();
            song.module = $(this).find('#module').val();
            song.price = $(this).find('#price').val();
            song.album = $(this).find('#album').val();
            song.genre = $(this).find('#genre').val();
            song.date = $(this).find('#date').val();
            song.field = $(this).find('#field').val();
            song.currency = $(this).find('#currency').val();
            song.url = $(this).find('#url').val();
            beatSlingerAdd(song);
        });

        function beatSlingerAdd(obj) {
            var p = playlist_1.playlist;
            if (p.length == 0) {
                playlist_1.add(obj, true);
            } else {

                var addOrNot = checkForSong(p, obj);
                if (addOrNot) {
                    console.log(addOrNot);
                    var pause = $('#jquery_jplayer_1').data().jPlayer.status.paused;
                    if (pause) {
                        playlist_1.add(obj, true);
                    } else {
                        playlist_1.add(obj);
                    }
                }
            }
        }

        function checkForSong(playlist, song) {
            // check if song is already in playlist
            var adding = true;
            $.each(playlist, function (index, value) {
                if (value.id == song.id) {
                    playlist_1.play(index);
                    adding = false;
                }
            });
            return adding;
        }

    });

    function showBeatInfo(song) {

        var title = song.title;
        title = decodeURIComponent(title);
        title = title.replace(/\+/g, ' ');

        var artist = song.artist;
        artist = decodeURIComponent(artist);
        artist = artist.replace(/\+/g, ' ');

        var album = song.album;
        album = decodeURIComponent(album);
        album = album.replace(/\+/g, ' ');

        var p = parseFloat(song.price).toFixed(2);
        var price = p + song.currency;


        $('.jp-data .jp-title2').text(title);
        $('.jp-data .jp-artist').text(artist);
        $('.jp-data .jp-album').text(album);
        $('.jp-data .jp-genre').text(song.genre);
        $('.jp-data .jp-date').text(song.date);
        $('.jrBeatSlinger_beat_player .jp-buy button')
                .text(price)
                .prop('disabled', false)
                .unbind('click')
                .click(function(){
            jrCore_window_location(song.item_url);
        });
        $('.jp-image .wrap').html('<a href="' + song.item_url + '"><img src="' + song.poster + '" class="img_scale" /></a>');

        if (song.price == 0) {
            $('.jrBeatSlinger_beat_player .jp-buy button').prop('disabled', 'disabled').unbind('click');
        }

        $('.audio_button').css({ opacity : 1, cursor : 'pointer'});
        $('span#audio_' + song.id).css({ opacity : 0.3, cursor : 'auto'});
    }

</script>

<div class="jrBeatSlinger_beat_player"
     onclick="event.cancelBubble = true; if(event.stopPropagation) event.stopPropagation();">
    <div id="jp_container_1" class="jp-audio">
        <div class="jp-type-playlist">
            <div id="jquery_jplayer_1" class="jp-jplayer"></div>
            <div class="jp-title">
                <ul>
                    <li></li>
                </ul>
            </div>
            <div class="jp-gui">
                <div class="jp-interface">
                    <div class="jp-controls-holder">
                        <ul class="jp-controls clearfix">
                            <li><a href="javascript:" class="jp-stop" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang module="jrAudio" id="3" default="stop"}"></a></li>
                            <li><a href="javascript:" class="jp-previous" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang module="jrAudio" id="44" default="previous"}"></a></li>
                            <li><a href="javascript:" class="jp-play" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang module="jrAudio" id="1" default="play"}"></a></li>
                            <li><a href="javascript:" class="jp-pause" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang module="jrAudio" id="2" default="pause"}"></a></li>
                            <li><a href="javascript:" class="jp-next" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang module="jrAudio" id="45" default="next"}"></a></li>
                            <li><a href="javascript:" class="jp-min" tabindex="{jrCore_next_tabindex}"
                                   title="{jrCore_lang skin="jrBeatSlinger" id="100" default="minimize"}"></a></li>
                        </ul>
                    </div>
                </div>

                <div class="wrap">
                    <div class="row" style="overflow: visible;">
                        <div class="col4 jp-info">
                            <div class="jp-image">
                                <div class="wrap"><!-- image goes here --></div>
                            </div>
                            <div class="jp-data">
                                <div class="wrap">
                                    <span class="jp-title2"></span>
                                    <span class="jp-artist"></span>
                                    <span class="jp-album"></span>
                                    <span class="jp-genre"></span>
                                    <span class="jp-date"></span>
                                </div>
                            </div>
                        </div>
                        <div class="col4 jp-clock" style="position: relative;">
                            <div class="wrap">
                                <div class="jp-time">
                                    <div class="jp-current-time"></div>
                                    <div>/</div>
                                    <div class="jp-duration"></div>
                                </div>
                                <div class="jp-progress-holder">
                                    <div class="jp-progress">
                                        <div class="jp-seek-bar">
                                            <div class="jp-play-bar"></div>
                                        </div>
                                    </div>
                                </div>
                                <ul class="jp-controls">
                                    <li><a href="javascript:" class="jp-mute" tabindex="{jrCore_next_tabindex}"
                                           title="{jrCore_lang module="jrAudio" id="4" default="mute"}"></a></li>
                                    <li><a href="javascript:" class="jp-unmute" tabindex="{jrCore_next_tabindex}"
                                           title="{jrCore_lang module="jrAudio" id="5" default="unmute"}"></a></li>
                                    <li><a href="javascript:" class="jp-volume-max" tabindex="{jrCore_next_tabindex}"
                                           title="{jrCore_lang module="jrAudio" id="6" default="max volume"}"></a></li>
                                    <li><a href="javascript:" class="jp-show" tabindex="{jrCore_next_tabindex}" title="Toggle Playlist"></a></li>
                                </ul>
                                <div class="jp-volume-bar">
                                    <div class="jp-volume-bar-value"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col4">
                            <div class="jp-buy">
                                <button></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="jp-playlist-bg" style="display: none;">
                    <div class="jp-playlist-wrap">
                        <div class="jp-playlist" style="display: block;">
                            <div class="jp-head">
                                <div style="padding: 10px 20px;">{$_conf.jrCore_system_name} {jrCore_lang skin="jrBeatSlinger" id=102 default="Playlist"}</div>
                                <div class="jp-close">
                                    <a href="#"></a>
                                </div>
                            </div>
                            <ul>
                                <li></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>