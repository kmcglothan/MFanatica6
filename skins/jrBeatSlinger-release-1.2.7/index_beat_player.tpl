
{if $_conf.jrBeatSlinger_player_active == 'on'}
    {* set up defaults or you'll get an error *}
    {$s1 = "_item_id > 0"}
    {$s2 = "_item_id > 0"}
    {$s3 = "_item_id > 0"}

    {* search 1 *}
    {if strlen($_conf.jrBeatSlinger_search_1) > 0}
        {$s1 = $_conf.jrBeatSlinger_search_1}
    {/if}

    {* search 2 *}
    {if strlen($_conf.jrBeatSlinger_search_2) > 0}
        {$s2 = $_conf.jrBeatSlinger_search_2}
    {/if}

    {* price required *}
    {if $_conf.jrBeatSlinger_required == 'on'}
        {$s3 = "audio_file_item_price > 0"}
    {/if}


    {* load media player *}
    {jrCore_media_player
        type="jrBeatSlinger_beat_player"
        search1=$s1
        search2=$s2
        search3=$s3
        module="jrAudio"
        field="audio_file"
        order_by=$_conf.jrBeatSlinger_song_order
        limit="100"
        autoplay=false
    }
{/if}
