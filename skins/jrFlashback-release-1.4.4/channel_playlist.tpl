{if isset($_conf.jrFlashback_auto_play) && $_conf.jrFlashback_auto_play == 'on'}
    {assign var="vap" value="true"}
{elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
    {assign var="vap" value="true"}
{else}
    {assign var="vap" value="false"}
{/if}
{assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
{assign var="player_type" value=$_conf.$skin_player_type}
{assign var="player" value="jrPlaylist_`$player_type`"}
{if isset($player_type) && strlen($player_type) > 0}
    {jrCore_media_player type=$player module="jrPlaylist" item=$item autoplay=$vap}
{else}
    {jrCore_media_player module="jrPlaylist" item=$item autoplay=$vap}
{/if}
