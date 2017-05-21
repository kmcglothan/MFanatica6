{if isset($_conf.jrProJam_auto_play) && $_conf.jrProJam_auto_play == 'on'}
    {assign var="sap" value="true"}
{elseif isset($_post.autoplay) && $_post.autoplay == 'true'}
    {assign var="sap" value="true"}
{else}
    {assign var="sap" value="false"}
{/if}
{assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
{assign var="player_type" value=$_conf.$skin_player_type}
{assign var="player" value="jrPlaylist_`$player_type`"}

{if isset($player_type) && strlen($player_type) > 0}
    {jrCore_media_player type=$player module="jrPlaylist" item=$item autoplay=$sap}
{else}
    {jrCore_media_player module="jrPlaylist" item=$item autoplay=$sap}
{/if}
