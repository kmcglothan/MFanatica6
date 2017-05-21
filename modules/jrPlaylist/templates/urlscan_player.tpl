{assign var="skin_player_type" value="`$_conf.jrCore_active_skin`_player_type"}
{assign var="player_type" value=$_conf.$skin_player_type}
{assign var="player" value="jrPlaylist_`$player_type`"}
{jrCore_media_player type=$player module="jrPlaylist" item_id=$_item_id autoplay=$autoplay width="100%" height="300"}
